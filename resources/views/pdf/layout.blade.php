<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <title>{{ $tajuk }}</title>
    <style>
        @page { margin: 14mm 12mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1f2233; }
        h1 { font-size: 15pt; margin: 0 0 2mm; }
        .meta { color: #6b7280; font-size: 8pt; margin-bottom: 5mm; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 0.4pt solid #d8dae5; padding: 1.6mm 2mm; text-align: left; vertical-align: top; }
        th { background: #f1f2f8; font-size: 8pt; text-transform: uppercase; letter-spacing: 0.4pt; }
        .kosong { color: #9ca3af; font-style: italic; }
        .rehat { background: #f1f2f8; text-align: center; letter-spacing: 1pt; color: #6b7280; }
        .kecil { font-size: 7.5pt; color: #6b7280; }
        .kanan { text-align: right; }
        .footer { position: fixed; bottom: -8mm; left: 0; right: 0; font-size: 7.5pt; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>{{ $tajuk }}</h1>
    <p class="meta">{{ $subtajuk }} · Dijana {{ now()->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>

    @yield('kandungan')

    <div class="footer">Jadual Kelas Student Auto</div>
</body>
</html>
