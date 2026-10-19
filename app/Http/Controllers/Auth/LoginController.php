<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ], [], [
            'login' => 'Email / NIP',
            'password' => 'Kata Sandi',
        ]);

        $remember = $request->boolean('remember');

        // 1) Coba akun lokal (email) — admin/guru/siswa yang dibuat di Manajemen User.
        $lokalBerhasil = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)
            && Auth::attempt(['email' => $credentials['login'], 'password' => $credentials['password']], $remember);

        // 2) Kalau bukan akun lokal, coba login guru (NIP) lalu siswa (NISN)
        //    memakai password dari database datacenter.
        if (! $lokalBerhasil
            && ! $this->loginGuruDatacenter($credentials['login'], $credentials['password'], $remember)
            && ! $this->loginSiswaDatacenter($credentials['login'], $credentials['password'], $remember)) {
            return back()->withErrors([
                'login' => 'Email/NIP/NISN atau kata sandi yang Anda masukkan salah.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        if (! $request->user()->aktif) {
            Auth::logout();
            return back()->withErrors([
                'login' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ]);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Autentikasi guru terhadap tabel "guru" database datacenter (NIP +
     * password yang sama dengan login di aplikasi datacenter). Bila cocok,
     * akun lokal role "guru" dibuat/disinkronkan otomatis lalu di-login-kan.
     */
    private function loginGuruDatacenter(string $login, string $password, bool $remember): bool
    {
        // NIP bisa dobel (mis. "-" / placeholder), jadi cek semua kandidat
        // dan ambil yang password-nya cocok.
        $guru = Guru::where('nip', $login)
            ->orWhere('email', $login)
            ->get()
            ->first(fn ($g) => $g->is_aktif && $g->password && Hash::check($password, $g->password));

        if (! $guru) {
            return false;
        }

        $user = User::firstOrNew(['guru_id' => $guru->id]);
        $user->name = $guru->nama_ptk;
        $user->role = User::ROLE_GURU;
        $user->aktif = true;
        $user->password = $password; // di-hash otomatis oleh cast "hashed"

        // Email lokal harus unik; pakai email guru bila ada dan belum dipakai
        // akun lain, lalu email sintetis berbasis NIP, terakhir berbasis id.
        $user->email = $this->emailUnik($user, [
            $guru->email,
            $guru->nip ? $guru->nip.'@guru.datacenter' : null,
            'guru'.$guru->id.'@guru.datacenter',
        ]);

        $user->save();

        Auth::login($user, $remember);

        return true;
    }

    /**
     * Autentikasi siswa terhadap tabel "siswa" database datacenter (NISN +
     * password yang sama dengan login di aplikasi datacenter). Bila cocok,
     * akun lokal role "siswa" dibuat/disinkronkan otomatis lalu di-login-kan.
     */
    private function loginSiswaDatacenter(string $login, string $password, bool $remember): bool
    {
        // NISN bisa dobel (mis. placeholder "99999"), jadi cek semua kandidat
        // dan ambil yang password-nya cocok.
        $siswa = Siswa::where('nisn', $login)
            ->orWhere('email', $login)
            ->get()
            ->first(fn ($s) => $s->is_aktif && $s->password && Hash::check($password, $s->password));

        if (! $siswa) {
            return false;
        }

        $user = User::firstOrNew(['siswa_id' => $siswa->id]);
        $user->name = $siswa->nama_siswa;
        $user->role = User::ROLE_SISWA;
        $user->aktif = true;
        $user->password = $password; // di-hash otomatis oleh cast "hashed"

        $user->email = $this->emailUnik($user, [
            $siswa->email,
            $siswa->nisn ? $siswa->nisn.'@siswa.datacenter' : null,
            'siswa'.$siswa->id.'@siswa.datacenter',
        ]);

        $user->save();

        Auth::login($user, $remember);

        return true;
    }

    /**
     * Ambil kandidat email pertama yang belum dipakai akun lain. Kandidat
     * terakhir berbasis id datacenter sehingga dijamin unik.
     */
    private function emailUnik(User $user, array $kandidat): string
    {
        $kandidat = array_values(array_filter($kandidat));

        foreach ($kandidat as $email) {
            $dipakai = User::where('email', $email)
                ->when($user->exists, fn ($q) => $q->where('id', '!=', $user->id))
                ->exists();

            if (! $dipakai) {
                return $email;
            }
        }

        return end($kandidat);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
