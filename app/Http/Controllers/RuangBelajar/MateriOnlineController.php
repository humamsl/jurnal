<?php

namespace App\Http\Controllers\RuangBelajar;

use App\Http\Controllers\Concerns\FilterPenugasanGuru;
use App\Http\Controllers\Crud\BaseCrudController;
use App\Models\MateriOnline;
use Illuminate\View\View;

class MateriOnlineController extends BaseCrudController
{
    use FilterPenugasanGuru;

    protected string $model = MateriOnline::class;
    protected string $routeName = 'ruangbelajar.materi';
    protected string $title = 'Materi Online';
    protected bool $hasDetail = true;

    protected function canManage(): bool
    {
        return auth()->user() && ! auth()->user()->isSiswa();
    }

    /**
     * Guru hanya melihat materi yang diunggah dirinya sendiri; siswa hanya
     * melihat materi untuk kelasnya.
     */
    protected function baseQuery()
    {
        $user = auth()->user();

        return parent::baseQuery()
            ->when($user && $user->isGuru() && $user->guru_id, fn ($q) => $q->where('pegawai_id', $user->guru_id))
            ->when($user && $user->isSiswa(), fn ($q) => $q->where('kelas_id', $user->kelasIdSiswa() ?? 0));
    }

    public function show(int|string $id): View
    {
        $item = $this->baseQuery()->with(['mataPelajaran', 'kelas', 'pegawai'])->findOrFail($id);

        return view('ruangbelajar.materi-show', [
            'item' => $item,
            'title' => $this->title,
            'routeName' => $this->routeName,
            'canManage' => $this->canManage(),
        ]);
    }

    protected function fields(): array
    {
        return [
            ['name' => 'judul', 'label' => 'Judul Materi', 'type' => 'text', 'rules' => 'required|string|max:150'],
            ['name' => 'mata_pelajaran_id', 'label' => 'Mata Pelajaran', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'mataPelajaran', 'model' => \App\Models\MataPelajaran::class, 'display' => 'nama_mapel'] + $this->idsFilter($this->mapelIdsGuru())],
            ['name' => 'kelas_id', 'label' => 'Kelas', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'kelas', 'model' => \App\Models\Kelas::class, 'display' => 'nama_rombel'] + $this->idsFilter($this->kelasIdsGuru())],
            ['name' => 'pegawai_id', 'label' => 'Guru Pengunggah', 'type' => 'select', 'rules' => 'nullable|integer', 'relation' => ['method' => 'pegawai', 'model' => \App\Models\Guru::class, 'display' => 'nama_ptk'] + $this->idsFilter(auth()->user()?->isGuru() && auth()->user()->guru_id ? [auth()->user()->guru_id] : null)],
            ['name' => 'file', 'label' => 'File Materi', 'type' => 'file', 'rules' => 'nullable|file|max:10240', 'list' => false],
            ['name' => 'link_video', 'label' => 'Link Video (opsional)', 'type' => 'text', 'rules' => 'nullable|url|max:255', 'list' => false],
            ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string', 'list' => false],
        ];
    }
}
