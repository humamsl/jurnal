<?php

namespace App\Http\Controllers\RuangBelajar;

use App\Http\Controllers\Crud\BaseCrudController;
use App\Models\Tugas;

class TugasController extends BaseCrudController
{
    protected string $model = Tugas::class;
    protected string $routeName = 'ruangbelajar.tugas';
    protected string $title = 'Tugas';

    protected function canManage(): bool
    {
        return auth()->user() && ! auth()->user()->isSiswa();
    }

    protected function fields(): array
    {
        return [
            ['name' => 'judul', 'label' => 'Judul Tugas', 'type' => 'text', 'rules' => 'required|string|max:150'],
            ['name' => 'mata_pelajaran_id', 'label' => 'Mata Pelajaran', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'mataPelajaran', 'model' => \App\Models\MataPelajaran::class, 'display' => 'nama_mapel']],
            ['name' => 'kelas_id', 'label' => 'Kelas', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'kelas', 'model' => \App\Models\Kelas::class, 'display' => 'nama_rombel']],
            ['name' => 'pegawai_id', 'label' => 'Guru Pemberi Tugas', 'type' => 'select', 'rules' => 'nullable|integer', 'relation' => ['method' => 'pegawai', 'model' => \App\Models\Guru::class, 'display' => 'nama_ptk']],
            ['name' => 'deskripsi', 'label' => 'Deskripsi / Instruksi', 'type' => 'textarea', 'rules' => 'nullable|string', 'list' => false],
            ['name' => 'file_lampiran', 'label' => 'File Lampiran', 'type' => 'file', 'rules' => 'nullable|file|max:10240', 'list' => false],
            ['name' => 'tanggal_mulai', 'label' => 'Tanggal Mulai', 'type' => 'date', 'rules' => 'nullable|date'],
            ['name' => 'tanggal_selesai', 'label' => 'Batas Akhir Pengumpulan', 'type' => 'date', 'rules' => 'nullable|date'],
        ];
    }
}
