<?php

declare(strict_types=1);

/*
 | Every reason the engine can emit. Reasons are stored as structured data and only
 | turned into Malay here, so fixing a wording never means re-running a solve.
 |
 | A Pest test walks the rule registry and asserts every code below exists, which
 | catches the classic "added a rule, forgot the Malay string" regression.
 */
return [
    'reasons' => [
        'pelajar_bermasalah' => [
            'guru_tegas' => 'cikgu tegas (:guru) kerana pelajar bermasalah',
            'guru_kurang_tegas' => 'cikgu :guru kurang tegas walaupun pelajar bermasalah',
            'terlalu_ramai_bermasalah' => 'sudah ada :bilangan pelajar bermasalah dalam :kelas',
            'tiada_guru' => ':kelas belum ada guru',
        ],

        'kelas_adik_beradik' => [
            'asing_dipenuhi' => 'adik-beradik :adik_beradik diasingkan',
            'asing_gagal' => 'terpaksa sekelas dengan adik-beradik :adik_beradik',
            'bersama_dipenuhi' => 'bersama adik-beradik :adik_beradik seperti diminta keluarga',
            'bersama_gagal' => 'tidak dapat disatukan dengan adik-beradik :adik_beradik',
        ],

        'seimbangkan_jantina' => [
            'imbangan_baik' => 'membantu imbangan jantina (:lelaki L / :perempuan P)',
            'imbangan_terjejas' => 'menjejaskan imbangan jantina dalam :kelas',
        ],

        'had_maksimum_kelas' => [
            'kelas_penuh' => ':kelas sudah penuh (had :had pelajar)',
        ],

        'kapasiti_bilik' => [
            'bilik_penuh' => 'bilik :kelas sudah penuh (kapasiti :kapasiti)',
        ],

        'padanan_tahun' => [
            'tahun_tidak_sepadan' => ':kelas bukan untuk pelajar Tahun :tahun',
        ],

        'sistem' => [
            'kekal_di_kelas_asal' => 'kekal di :kelas',
            'disematkan_oleh_admin' => 'disemat oleh admin, tidak dialihkan',
            'tiada_kelas_tahun' => 'tiada kelas dibuka untuk Tahun :tahun',
        ],
    ],

    'sentence' => [
        'place' => 'Diletakkan dalam :kelas: :sebab',
        'move' => 'Dipindah dari :dari ke :kelas: :sebab',
        'keep' => 'Kekal dalam :kelas: :sebab',
        'pinned' => 'Disemat dalam :kelas: :sebab',
        'unplaceable' => 'Tidak dapat ditempatkan: :sebab',
    ],

    'fallback' => [
        'place' => 'Diletakkan dalam :kelas.',
        'move' => 'Dipindah dari :dari ke :kelas.',
        'keep' => 'Kekal dalam :kelas.',
        'pinned' => 'Disemat dalam :kelas oleh admin.',
        'unplaceable' => 'Tidak dapat ditempatkan.',
    ],

    'tradeoff' => [
        // The honest answer to "why did it break my rule": because every alternative
        // scored worse.
        'no_better_option' => 'tiada kelas lain yang lebih sesuai',
    ],

    'actions' => [
        'place' => 'Ditempatkan',
        'move' => 'Dipindah',
        'keep' => 'Kekal',
        'pinned' => 'Disemat',
        'unplaceable' => 'Tidak dapat ditempatkan',
    ],

    'modes' => [
        'fill_only' => 'Isi tempat kosong sahaja',
        'rebalance' => 'Imbang semula (boleh memindahkan pelajar sedia ada)',
    ],
];
