<?php

namespace App\Http\Controllers\RuangBelajar;

<<<<<<< HEAD
=======
use App\Http\Controllers\Concerns\FilterPenugasanGuru;
>>>>>>> ef6026ffe285d4d1bfa9854402ab9ac13ce5328f
use App\Http\Controllers\Crud\BaseCrudController;
use App\Models\MateriOnline;

class MateriOnlineController extends BaseCrudController
{
<<<<<<< HEAD
=======
    use FilterPenugasanGuru;

>>>>>>> ef6026ffe285d4d1bfa9854402ab9ac13ce5328f
    protected string $model = MateriOnline::class;
    protected string $routeName = 'ruangbelajar.materi';
    protected string $title = 'Materi Online';

    protected function canManage(): bool
    {
        return auth()->user() && ! auth()->user()->isSiswa();
    }

<<<<<<< HEAD
=======
    /** Guru hanya melihat materi yang diunggah dirinya sendiri. */
    protected function baseQuery()
    {
        $user = auth()->user();

        return parent::baseQuery()
            ->when($user && $user->isGuru() && $user->guru_id, fn ($q) => $q->where('pegawai_id', $user->guru_id));
    }

>>>>>>> ef6026ffe285d4d1bfa9854402ab9ac13ce5328f
    protected function fields(): array
    {
        return [
            ['name' => 'judul', 'label' => 'Judul Materi', 'type' => 'text', 'rules' => 'required|string|max:150'],
<<<<<<< HEAD
            ['name' => 'mata_pelajaran_id', 'label' => 'Mata Pelajaran', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'mataPelajaran', 'model' => \App\Models\MataPelajaran::class, 'display' => 'nama_mapel']],
            ['name' => 'kelas_id', 'label' => 'Kelas', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'kelas', 'model' => \App\Models\Kelas::class, 'display' => 'nama_rombel']],
            ['name' => 'pegawai_id', 'label' => 'Guru Pengunggah', 'type' => 'select', 'rules' => 'nullable|integer', 'relation' => ['method' => 'pegawai', 'model' => \App\Models\Guru::class, 'display' => 'nama_ptk']],
=======
            ['name' => 'mata_pelajaran_id', 'label' => 'Mata Pelajaran', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'mataPelajaran', 'model' => \App\Models\MataPelajaran::class, 'display' => 'nama_mapel'] + $this->idsFilter($this->mapelIdsGuru())],
            ['name' => 'kelas_id', 'label' => 'Kelas', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'kelas', 'model' => \App\Models\Kelas::class, 'display' => 'nama_rombel'] + $this->idsFilter($this->kelasIdsGuru())],
            ['name' => 'pegawai_id', 'label' => 'Guru Pengunggah', 'type' => 'select', 'rules' => 'nullable|integer', 'relation' => ['method' => 'pegawai', 'model' => \App\Models\Guru::class, 'display' => 'nama_ptk'] + $this->idsFilter(auth()->user()?->isGuru() && auth()->user()->guru_id ? [auth()->user()->guru_id] : null)],
>>>>>>> ef6026ffe285d4d1bfa9854402ab9ac13ce5328f
            ['name' => 'file', 'label' => 'File Materi', 'type' => 'file', 'rules' => 'nullable|file|max:10240', 'list' => false],
            ['name' => 'link_video', 'label' => 'Link Video (opsional)', 'type' => 'text', 'rules' => 'nullable|url|max:255', 'list' => false],
            ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string', 'list' => false],
        ];
    }
}
