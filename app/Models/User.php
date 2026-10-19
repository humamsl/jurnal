<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_GURU = 'guru';
    const ROLE_SISWA = 'siswa';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pegawai_id',
        'guru_id',
        'siswa_id',
        'aktif',
    ];

    /** Cache per-request untuk kelasIdSiswa(). */
    private ?int $kelasIdSiswaCache = null;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'aktif' => 'boolean',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /** Guru datacenter yang tertaut ke akun ini (login via NIP datacenter). */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isGuru(): bool
    {
        return $this->role === self::ROLE_GURU;
    }

    public function isSiswa(): bool
    {
        return $this->role === self::ROLE_SISWA;
    }

    /**
     * Id kelas (rombongan_belajar datacenter) siswa saat ini, dipakai untuk
     * membatasi materi/tugas yang boleh dilihat siswa. Null bila belum
     * ditempatkan di rombel mana pun.
     */
    public function kelasIdSiswa(): ?int
    {
        if (! $this->isSiswa() || ! $this->siswa_id) {
            return null;
        }

        return $this->kelasIdSiswaCache ??= SiswaRombel::where('siswa_id', $this->siswa_id)
            ->orderByDesc('tahun_ajaran_id')
            ->value('rombongan_belajar_id');
    }
}
