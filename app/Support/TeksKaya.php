<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Render teks bebas (deskripsi materi/tugas, dll.) yang bisa berupa teks
 * biasa ATAU HTML (mis. "<p>...</p>" hasil tempel dari editor/aplikasi lain).
 *
 * - Teks biasa: di-escape, baris baru jadi <br>.
 * - HTML: dibersihkan dengan HTMLPurifier (hanya tag format aman; script,
 *   atribut on*, javascript: dsb. dibuang) supaya tag tidak tampil mentah
 *   di halaman tetapi juga tidak membuka celah XSS.
 */
class TeksKaya
{
    private const TAG_DIIZINKAN = 'p,br,b,strong,i,em,u,s,sub,sup,ul,ol,li,a[href|title],'
        .'h1,h2,h3,h4,h5,h6,blockquote,pre,code,span,div,hr,'
        .'table,thead,tbody,tr,th,td,img[src|alt]';

    private static ?\HTMLPurifier $purifier = null;

    public static function html(mixed $teks): string
    {
        $teks = self::keString($teks);

        if ($teks === strip_tags($teks)) {
            return nl2br(e($teks));
        }

        // HTML tanpa tag blok/baris (mis. "baris 1\n<b>baris 2</b>") tetap
        // mempertahankan baris barunya.
        if (! preg_match('~<(p|br|div|li|h[1-6]|table|pre|blockquote)\b~i', $teks)) {
            $teks = nl2br($teks);
        }

        if (! class_exists(\HTMLPurifier::class)) {
            return nl2br(e(trim(html_entity_decode(strip_tags($teks)))));
        }

        return self::purifier()->purify($teks);
    }

    /** Versi teks polos satu baris, untuk ringkasan di tabel. */
    public static function ringkas(mixed $teks, int $panjang = 60): string
    {
        $polos = html_entity_decode(strip_tags(self::keString($teks)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim(preg_replace('/\s+/u', ' ', $polos)), $panjang);
    }

    private static function keString(mixed $nilai): string
    {
        return is_scalar($nilai) || $nilai instanceof \Stringable ? (string) $nilai : '';
    }

    private static function purifier(): \HTMLPurifier
    {
        if (self::$purifier) {
            return self::$purifier;
        }

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.Allowed', self::TAG_DIIZINKAN);
        $config->set('HTML.TargetBlank', true);
        $config->set('AutoFormat.RemoveEmpty', true);

        return self::$purifier = new \HTMLPurifier($config);
    }
}
