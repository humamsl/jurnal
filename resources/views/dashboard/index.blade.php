@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<p class="text-muted">Selamat datang, <strong>{{ auth()->user()->name }}</strong>.</p>

@can('admin')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-person-badge fs-3 text-primary"></i>
            <div class="fs-4 fw-bold">{{ $stats['pegawai'] }}</div>
            <div class="text-muted small">Pegawai</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-people fs-3 text-success"></i>
            <div class="fs-4 fw-bold">{{ $stats['siswa'] }}</div>
            <div class="text-muted small">Siswa</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-journal-bookmark fs-3 text-warning"></i>
            <div class="fs-4 fw-bold">{{ $stats['mata_pelajaran'] }}</div>
            <div class="text-muted small">Mata Pelajaran</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center py-3">
            <i class="bi bi-easel fs-3 text-info"></i>
            <div class="fs-4 fw-bold">{{ $stats['guru_mapel'] }}</div>
            <div class="text-muted small">Penugasan Guru Mapel</div>
        </div>
    </div>
</div>
@endcan

@if(!auth()->user()->isAdmin())
<div class="card shadow-sm">
    <div class="card-header bg-white">Tugas Terbaru</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Judul</th><th>Mata Pelajaran</th><th>Kelas</th><th>Batas Akhir</th></tr>
            </thead>
            <tbody>
                @forelse($tugasTerbaru as $t)
                <tr>
                    <td>{{ $t->judul }}</td>
                    <td>{{ $t->mataPelajaran->nama_mapel ?? '-' }}</td>
                    <td>{{ $t->kelas->nama_rombel ?? '-' }}</td>
                    <td>{{ optional($t->tanggal_selesai)->translatedFormat('d M Y') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada tugas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
