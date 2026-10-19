<?php

namespace App\Http\Controllers\RuangBelajar;

use App\Http\Controllers\Concerns\FilterPenugasanGuru;
use App\Http\Controllers\Crud\BaseCrudController;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TugasController extends BaseCrudController
{
    use FilterPenugasanGuru;

    protected string $model = Tugas::class;
    protected string $routeName = 'ruangbelajar.tugas';
    protected string $title = 'Tugas';
    protected bool $hasDetail = true;

    protected function canManage(): bool
    {
        return auth()->user() && ! auth()->user()->isSiswa();
    }

    /**
     * Guru hanya melihat tugas yang dibuat dirinya sendiri; siswa hanya
     * melihat tugas untuk kelasnya.
     */
    protected function baseQuery()
    {
        $user = auth()->user();

        return parent::baseQuery()
            ->when($user && $user->isGuru() && $user->guru_id, fn ($q) => $q->where('pegawai_id', $user->guru_id))
            ->when($user && $user->isSiswa(), fn ($q) => $q->where('kelas_id', $user->kelasIdSiswa() ?? 0));
    }

    /**
     * Detail tugas. Siswa: instruksi + form pengumpulan miliknya.
     * Guru/admin: instruksi + daftar pengumpulan seluruh siswa di kelas.
     */
    public function show(int|string $id): View
    {
        $tugas = $this->baseQuery()->with(['mataPelajaran', 'kelas', 'pegawai'])->findOrFail($id);
        $user = auth()->user();

        $data = [
            'tugas' => $tugas,
            'title' => $this->title,
            'routeName' => $this->routeName,
            'canManage' => $this->canManage(),
        ];

        if ($user->isSiswa()) {
            $data['pengumpulan'] = $tugas->pengumpulan()->where('siswa_id', $user->siswa_id)->first();

            return view('ruangbelajar.tugas-show', $data);
        }

        $pengumpulan = $tugas->pengumpulan()->get()->keyBy('siswa_id');

        // Semua siswa di kelas tugas (rombel datacenter), plus pengumpul yang
        // sudah tidak terdaftar di kelas itu (mis. pindah kelas).
        $siswaKelas = SiswaRombel::with('siswa')
            ->where('rombongan_belajar_id', $tugas->kelas_id)
            ->get()
            ->pluck('siswa')
            ->filter()
            ->keyBy('id');
        $pengumpulLain = Siswa::whereIn('id', $pengumpulan->keys()->diff($siswaKelas->keys()))->get();

        $data['daftar'] = $siswaKelas->values()->concat($pengumpulLain)
            ->sortBy('nama_siswa', SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn ($siswa) => ['siswa' => $siswa, 'pengumpulan' => $pengumpulan->get($siswa->id)])
            ->values();
        $data['jumlahMengumpulkan'] = $pengumpulan->count();

        return view('ruangbelajar.tugas-show', $data);
    }

    /** Siswa mengumpulkan / memperbarui jawaban tugas (selama belum dinilai). */
    public function kumpulkan(Request $request, int|string $id): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->isSiswa() && $user->siswa_id, 403);

        $tugas = $this->baseQuery()->findOrFail($id);

        $pengumpulan = PengumpulanTugas::firstOrNew(['tugas_id' => $tugas->id, 'siswa_id' => $user->siswa_id]);

        if ($pengumpulan->sudahDinilai()) {
            return back()->with('error', 'Tugas sudah dinilai guru sehingga tidak bisa diubah lagi.');
        }

        $validated = $request->validate([
            'jawaban' => ['nullable', 'string', 'max:20000'],
            'file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,zip,rar'],
        ], [], [
            'jawaban' => 'Jawaban',
            'file' => 'File Jawaban',
        ]);

        if (blank($validated['jawaban'] ?? null) && ! $request->hasFile('file') && ! $pengumpulan->file) {
            return back()->withInput()->with('error', 'Isi jawaban atau unggah file terlebih dahulu.');
        }

        if ($request->hasFile('file')) {
            if ($pengumpulan->file) {
                Storage::disk('public')->delete($pengumpulan->file);
            }
            $pengumpulan->file = $request->file('file')->store('uploads/pengumpulan_tugas', 'public');
        }

        $pengumpulan->jawaban = $validated['jawaban'] ?? null;
        $pengumpulan->dikumpulkan_at = now();
        $pengumpulan->save();

        return redirect()->route($this->routeName.'.show', $tugas->id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /** Guru/admin memberi nilai & catatan untuk satu pengumpulan. */
    public function nilai(Request $request, int|string $id, int|string $pengumpulanId): RedirectResponse
    {
        abort_unless($this->canManage(), 403);

        $tugas = $this->baseQuery()->findOrFail($id);
        $pengumpulan = $tugas->pengumpulan()->findOrFail($pengumpulanId);

        $validated = $request->validate([
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan_guru' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'nilai' => 'Nilai',
            'catatan_guru' => 'Catatan',
        ]);

        $pengumpulan->update($validated + ['dinilai_at' => now()]);

        return redirect()->route($this->routeName.'.show', $tugas->id)
            ->with('success', 'Nilai berhasil disimpan.');
    }

    protected function fields(): array
    {
        return [
            ['name' => 'judul', 'label' => 'Judul Tugas', 'type' => 'text', 'rules' => 'required|string|max:150'],
            ['name' => 'mata_pelajaran_id', 'label' => 'Mata Pelajaran', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'mataPelajaran', 'model' => \App\Models\MataPelajaran::class, 'display' => 'nama_mapel'] + $this->idsFilter($this->mapelIdsGuru())],
            ['name' => 'kelas_id', 'label' => 'Kelas', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'kelas', 'model' => \App\Models\Kelas::class, 'display' => 'nama_rombel'] + $this->idsFilter($this->kelasIdsGuru())],
            ['name' => 'pegawai_id', 'label' => 'Guru Pemberi Tugas', 'type' => 'select', 'rules' => 'nullable|integer', 'relation' => ['method' => 'pegawai', 'model' => \App\Models\Guru::class, 'display' => 'nama_ptk'] + $this->idsFilter(auth()->user()?->isGuru() && auth()->user()->guru_id ? [auth()->user()->guru_id] : null)],
            ['name' => 'deskripsi', 'label' => 'Deskripsi / Instruksi', 'type' => 'textarea', 'rules' => 'nullable|string', 'list' => false],
            ['name' => 'file_lampiran', 'label' => 'File Lampiran', 'type' => 'file', 'rules' => 'nullable|file|max:10240', 'list' => false],
            ['name' => 'tanggal_mulai', 'label' => 'Tanggal Mulai', 'type' => 'date', 'rules' => 'nullable|date'],
            ['name' => 'tanggal_selesai', 'label' => 'Batas Akhir Pengumpulan', 'type' => 'date', 'rules' => 'nullable|date'],
        ];
    }
}
