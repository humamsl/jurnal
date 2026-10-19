<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'jawaban',
        'file',
        'dikumpulkan_at',
        'nilai',
        'catatan_guru',
        'dinilai_at',
    ];

    protected $casts = [
        'dikumpulkan_at' => 'datetime',
        'dinilai_at' => 'datetime',
        'nilai' => 'decimal:2',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    /** Siswa pengumpul — diambil dari tabel "siswa" database datacenter. */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function sudahDinilai(): bool
    {
        return $this->nilai !== null;
    }

    /** Terlambat bila dikumpulkan setelah hari batas akhir pengumpulan. */
    public function terlambat(): bool
    {
        $batas = $this->tugas?->tanggal_selesai;

        return $batas && $this->dikumpulkan_at && $this->dikumpulkan_at->gt($batas->copy()->endOfDay());
    }
}
