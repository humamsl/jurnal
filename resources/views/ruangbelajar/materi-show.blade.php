@extends('layouts.app')
@section('title', $title)
@section('content')
@php
    $ext = $item->file ? strtolower(pathinfo($item->file, PATHINFO_EXTENSION)) : null;
    $fileUrl = $item->file ? Storage::url($item->file) : null;

    // Link YouTube (watch, youtu.be, shorts, embed) ditampilkan sebagai player.
    $youtubeId = null;
    if ($item->link_video && preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{11})~', $item->link_video, $m)) {
        $youtubeId = $m[1];
    }
@endphp

<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="{{ route($routeName.'.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    @if($canManage)
    <a href="{{ route($routeName.'.edit', $item->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Ubah</a>
    @endif
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h2 class="h5 fw-bold mb-2">{{ $item->judul }}</h2>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge bg-primary">{{ optional($item->mataPelajaran)->nama_mapel ?? '-' }}</span>
            <span class="badge text-bg-light border">{{ optional($item->kelas)->nama_rombel ?? '-' }}</span>
        </div>
        <div class="text-muted small mb-3">
            <i class="bi bi-person me-1"></i>{{ optional($item->pegawai)->nama_ptk ?? '-' }}
            <span class="mx-2">&middot;</span>
            <i class="bi bi-calendar3 me-1"></i>{{ $item->created_at?->format('d/m/Y H:i') }}
        </div>

        @if($item->deskripsi)
            <div class="mb-0">{!! \App\Support\TeksKaya::html($item->deskripsi) !!}</div>
        @else
            <div class="text-muted fst-italic">Tidak ada deskripsi.</div>
        @endif
    </div>
</div>

@if($item->link_video)
<div class="card shadow-sm mb-4">
    <div class="card-header"><i class="bi bi-play-btn me-2"></i>Video</div>
    <div class="card-body">
        @if($youtubeId)
            <div class="ratio ratio-16x9 mb-2">
                <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" title="{{ $item->judul }}" allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
        @endif
        <a href="{{ $item->link_video }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-box-arrow-up-right me-1"></i>Buka di tab baru
        </a>
    </div>
</div>
@endif

@if($fileUrl)
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <span><i class="bi bi-file-earmark me-2"></i>File Materi</span>
        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-primary" download><i class="bi bi-download me-1"></i>Unduh</a>
    </div>
    @if($ext === 'pdf')
        <div class="ratio" style="--bs-aspect-ratio: 120%;">
            <iframe src="{{ $fileUrl }}" title="{{ $item->judul }}"></iframe>
        </div>
    @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
        <div class="card-body text-center">
            <img src="{{ $fileUrl }}" alt="{{ $item->judul }}" class="img-fluid rounded">
        </div>
    @else
        <div class="card-body text-muted small">
            Pratinjau tidak tersedia untuk file .{{ $ext }} — silakan unduh untuk membukanya.
        </div>
    @endif
</div>
@endif
@endsection
