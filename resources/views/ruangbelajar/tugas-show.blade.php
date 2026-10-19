@extends('layouts.app')
@section('title', $title)
@section('content')
@php
    $lampiranUrl = $tugas->file_lampiran ? Storage::url($tugas->file_lampiran) : null;
    $dibuka = $tugas->masihDibuka();
@endphp

<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="{{ route($routeName.'.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    @if($canManage)
    <a href="{{ route($routeName.'.edit', $tugas->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Ubah Tugas</a>
    @endif
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- ===== Detail tugas ===== --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
            <h2 class="h5 fw-bold mb-0">{{ $tugas->judul }}</h2>
            @if($dibuka)
                <span class="badge bg-success align-self-start">Masih dibuka</span>
            @else
                <span class="badge bg-secondary align-self-start">Batas waktu lewat</span>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge bg-primary">{{ optional($tugas->mataPelajaran)->nama_mapel ?? '-' }}</span>
            <span class="badge text-bg-light border">{{ optional($tugas->kelas)->nama_rombel ?? '-' }}</span>
        </div>
        <div class="row g-2 small text-muted mb-3">
            <div class="col-sm-4"><i class="bi bi-person me-1"></i>{{ optional($tugas->pegawai)->nama_ptk ?? '-' }}</div>
            <div class="col-sm-4"><i class="bi bi-calendar-event me-1"></i>Mulai: {{ $tugas->tanggal_mulai?->format('d/m/Y') ?? '-' }}</div>
            <div class="col-sm-4"><i class="bi bi-alarm me-1"></i>Batas: {{ $tugas->tanggal_selesai?->format('d/m/Y') ?? 'Tanpa batas' }}</div>
        </div>

        <h3 class="h6 fw-semibold">Instruksi</h3>
        @if($tugas->deskripsi)
            <div class="mb-3">{!! nl2br(e($tugas->deskripsi)) !!}</div>
        @else
            <div class="text-muted fst-italic mb-3">Tidak ada instruksi tertulis.</div>
        @endif

        @if($lampiranUrl)
            <a href="{{ $lampiranUrl }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-paperclip me-1"></i>Unduh Lampiran</a>
        @endif
    </div>
</div>

@if(auth()->user()->isSiswa())
{{-- ===== Pengumpulan milik siswa ===== --}}
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <span><i class="bi bi-upload me-2"></i>Pengumpulan Saya</span>
        @if(! $pengumpulan)
            <span class="badge bg-warning text-dark">Belum mengumpulkan</span>
        @elseif($pengumpulan->sudahDinilai())
            <span class="badge bg-success">Dinilai</span>
        @elseif($pengumpulan->terlambat())
            <span class="badge bg-danger">Terlambat</span>
        @else
            <span class="badge bg-info text-dark">Sudah dikumpulkan</span>
        @endif
    </div>
    <div class="card-body">
        @if($pengumpulan)
            <div class="small text-muted mb-3">
                Dikumpulkan: {{ $pengumpulan->dikumpulkan_at?->format('d/m/Y H:i') }}
            </div>
        @endif

        @if($pengumpulan && $pengumpulan->sudahDinilai())
            <div class="p-3 rounded mb-3" style="background: var(--brand-50);">
                <div class="small text-muted">Nilai</div>
                <div class="display-6 fw-bold text-primary">{{ rtrim(rtrim(number_format($pengumpulan->nilai, 2, ',', ''), '0'), ',') }}</div>
                @if($pengumpulan->catatan_guru)
                    <div class="mt-2"><span class="small text-muted d-block">Catatan guru</span>{!! nl2br(e($pengumpulan->catatan_guru)) !!}</div>
                @endif
            </div>
            @if($pengumpulan->jawaban)
                <h3 class="h6 fw-semibold">Jawaban</h3>
                <div class="mb-3">{!! nl2br(e($pengumpulan->jawaban)) !!}</div>
            @endif
            @if($pengumpulan->file)
                <a href="{{ Storage::url($pengumpulan->file) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark me-1"></i>File Jawaban</a>
            @endif
        @else
            @unless($dibuka)
                <div class="alert alert-warning py-2 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>Batas waktu sudah lewat. Tugas masih bisa dikumpulkan, tetapi akan ditandai <strong>terlambat</strong>.
                </div>
            @endunless
            <form action="{{ route($routeName.'.kumpulkan', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Jawaban</label>
                    <textarea name="jawaban" rows="6" class="form-control" placeholder="Tulis jawaban Anda di sini...">{{ old('jawaban', $pengumpulan?->jawaban) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">File Jawaban <span class="text-muted small">(opsional, maks. 10 MB)</span></label>
                    <input type="file" name="file" class="form-control"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar">
                    @if($pengumpulan?->file)
                        <div class="form-text">
                            File saat ini: <a href="{{ Storage::url($pengumpulan->file) }}" target="_blank">lihat file</a>. Unggah file baru untuk menggantinya.
                        </div>
                    @endif
                </div>
                <button class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>{{ $pengumpulan ? 'Perbarui Jawaban' : 'Kumpulkan Tugas' }}
                </button>
            </form>
        @endif
    </div>
</div>
@else
{{-- ===== Daftar pengumpulan untuk guru/admin ===== --}}
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <span><i class="bi bi-people me-2"></i>Pengumpulan Siswa</span>
        <span class="badge bg-primary">{{ $jumlahMengumpulkan }} / {{ $daftar->count() }} mengumpulkan</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Nama Siswa</th>
                    <th>Status</th>
                    <th>Jawaban</th>
                    <th style="min-width:280px;">Nilai &amp; Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftar as $row)
                    @php
                        $siswa = $row['siswa'];
                        $p = $row['pengumpulan'];
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $siswa->nama_siswa }}</div>
                            <div class="small text-muted">{{ $siswa->nisn ?: '-' }}</div>
                        </td>
                        <td class="text-nowrap">
                            @if(! $p)
                                <span class="badge bg-warning text-dark">Belum</span>
                            @else
                                @if($p->sudahDinilai())
                                    <span class="badge bg-success">Dinilai</span>
                                @elseif($p->terlambat())
                                    <span class="badge bg-danger">Terlambat</span>
                                @else
                                    <span class="badge bg-info text-dark">Dikumpulkan</span>
                                @endif
                                <div class="small text-muted">{{ $p->dikumpulkan_at?->format('d/m/Y H:i') }}</div>
                            @endif
                        </td>
                        <td>
                            @if($p)
                                @if($p->jawaban)
                                    <details>
                                        <summary class="small">{{ Illuminate\Support\Str::limit($p->jawaban, 50) }}</summary>
                                        <div class="small mt-1">{!! nl2br(e($p->jawaban)) !!}</div>
                                    </details>
                                @endif
                                @if($p->file)
                                    <a href="{{ Storage::url($p->file) }}" target="_blank" class="small"><i class="bi bi-paperclip"></i>File jawaban</a>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p)
                                <form action="{{ route($routeName.'.nilai', [$tugas->id, $p->id]) }}" method="POST" class="d-flex flex-wrap gap-1">
                                    @csrf @method('PUT')
                                    <input type="number" name="nilai" min="0" max="100" step="0.01" required
                                           value="{{ $p->nilai !== null ? rtrim(rtrim($p->nilai, '0'), '.') : '' }}"
                                           class="form-control form-control-sm" style="width:80px;" placeholder="0-100">
                                    <input type="text" name="catatan_guru" value="{{ $p->catatan_guru }}" maxlength="2000"
                                           class="form-control form-control-sm flex-grow-1" style="min-width:120px; width:auto;" placeholder="Catatan (opsional)">
                                    <button class="btn btn-sm btn-primary" title="Simpan nilai"><i class="bi bi-check-lg"></i></button>
                                </form>
                            @else
                                <span class="text-muted small">Menunggu pengumpulan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada siswa terdaftar di kelas ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
