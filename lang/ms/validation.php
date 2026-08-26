<?php

declare(strict_types=1);

/*
 | Laravel ships no Malay translations, so the rules this app actually uses are
 | translated here. Anything not listed falls back to English via APP_FALLBACK_LOCALE.
 */
return [
    'accepted' => ':attribute mesti diterima.',
    'after' => ':attribute mesti tarikh selepas :date.',
    'after_or_equal' => ':attribute mesti tarikh pada atau selepas :date.',
    'array' => ':attribute mesti berbentuk senarai.',
    'before' => ':attribute mesti tarikh sebelum :date.',
    'before_or_equal' => ':attribute mesti tarikh pada atau sebelum :date.',
    'between' => [
        'array' => ':attribute mesti mengandungi antara :min dan :max item.',
        'file' => 'Saiz :attribute mesti antara :min dan :max kilobait.',
        'numeric' => ':attribute mesti antara :min dan :max.',
        'string' => ':attribute mesti antara :min dan :max aksara.',
    ],
    'boolean' => ':attribute mesti benar atau salah.',
    'confirmed' => 'Pengesahan :attribute tidak sepadan.',
    'date' => ':attribute bukan tarikh yang sah.',
    'date_format' => ':attribute tidak mengikut format :format.',
    'different' => ':attribute dan :other mesti berlainan.',
    'digits' => ':attribute mesti :digits digit.',
    'email' => ':attribute mesti alamat emel yang sah.',
    'enum' => ':attribute yang dipilih tidak sah.',
    'exists' => ':attribute yang dipilih tidak sah.',
    'file' => ':attribute mesti sebuah fail.',
    'filled' => ':attribute mesti diisi.',
    'gt' => [
        'numeric' => ':attribute mesti lebih besar daripada :value.',
        'string' => ':attribute mesti lebih panjang daripada :value aksara.',
    ],
    'image' => ':attribute mesti sebuah imej.',
    'in' => ':attribute yang dipilih tidak sah.',
    'integer' => ':attribute mesti nombor bulat.',
    'max' => [
        'array' => ':attribute tidak boleh lebih daripada :max item.',
        'file' => 'Saiz :attribute tidak boleh melebihi :max kilobait.',
        'numeric' => ':attribute tidak boleh lebih daripada :max.',
        'string' => ':attribute tidak boleh melebihi :max aksara.',
    ],
    'mimes' => ':attribute mesti fail berjenis: :values.',
    'min' => [
        'array' => ':attribute mesti sekurang-kurangnya :min item.',
        'file' => 'Saiz :attribute mesti sekurang-kurangnya :min kilobait.',
        'numeric' => ':attribute mesti sekurang-kurangnya :min.',
        'string' => ':attribute mesti sekurang-kurangnya :min aksara.',
    ],
    'numeric' => ':attribute mesti nombor.',
    'regex' => 'Format :attribute tidak sah.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi apabila :other ialah :value.',
    'required_without' => ':attribute wajib diisi apabila :values tiada.',
    'same' => ':attribute dan :other mesti sepadan.',
    'string' => ':attribute mesti teks.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal dimuat naik.',
    'url' => ':attribute mesti URL yang sah.',

    'attributes' => [
        'name' => 'nama',
        'email' => 'emel',
        'password' => 'kata laluan',
        'phone' => 'telefon',
        'role' => 'peranan',
        'mode' => 'mod',
        'weight' => 'keutamaan',
        'config' => 'konfigurasi',
        'is_active' => 'status aktif',
        'capacity' => 'kapasiti',
        'location' => 'lokasi',
        'stream' => 'aliran',
        'year_level' => 'tahun',
        'meetings' => 'waktu pertemuan',
    ],
];
