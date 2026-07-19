<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Crud\BaseCrudController;
use App\Models\PemetaanCpTpAtp;

class PemetaanCpTpAtpController extends BaseCrudController
{
    protected string $model = PemetaanCpTpAtp::class;
    protected string $routeName = 'kurikulum.cp-tp-atp';
    protected string $title = 'Pemetaan CP-TP-ATP';

    protected function extraActions(): array
    {
        return [
            ['label' => 'Import Excel', 'url' => route('kurikulum.cp-tp-atp-import.form'), 'icon' => 'bi-upload'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'mata_pelajaran_id', 'label' => 'Mata Pelajaran', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'mataPelajaran', 'model' => \App\Models\MataPelajaran::class, 'display' => 'nama_mapel']],
            ['name' => 'tingkat_kelas_id', 'label' => 'Tingkat Kelas', 'type' => 'select', 'rules' => 'required|integer', 'relation' => ['method' => 'tingkatKelas', 'model' => \App\Models\TingkatKelas::class, 'display' => 'nama']],
            ['name' => 'fase', 'label' => 'Fase', 'type' => 'select', 'rules' => 'nullable|string', 'options' => ['A' => 'Fase A', 'B' => 'Fase B', 'C' => 'Fase C', 'D' => 'Fase D', 'E' => 'Fase E', 'F' => 'Fase F']],
            ['name' => 'capaian_pembelajaran', 'label' => 'Capaian Pembelajaran (CP)', 'type' => 'textarea', 'rules' => 'required|string'],
            ['name' => 'tujuan_pembelajaran', 'label' => 'Tujuan Pembelajaran (TP)', 'type' => 'textarea', 'rules' => 'required|string'],
            ['name' => 'alur_tujuan_pembelajaran', 'label' => 'Alur Tujuan Pembelajaran (ATP)', 'type' => 'textarea', 'rules' => 'required|string'],
            ['name' => 'tahun_ajaran', 'label' => 'Tahun Ajaran', 'type' => 'select', 'rules' => 'nullable|string|max:20', 'optionsFrom' => ['model' => \App\Models\TahunAjaran::class, 'column' => 'nama_tahun_ajaran']],
        ];
    }
}
