@extends('pdf.layout')

@section('kandungan')
    <table>
        <thead>
            <tr>
                <th style="width: 12%">Kod</th>
                <th>Nama</th>
                <th style="width: 14%">Jantina</th>
                <th style="width: 18%">Tingkah Laku</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelajar as $p)
                <tr>
                    <td class="kecil">{{ $p['kod'] }}</td>
                    <td>{{ $p['nama'] }}{{ $p['disemat'] ? ' (disemat)' : '' }}</td>
                    <td>{{ $p['jantina'] === 'L' ? 'Lelaki' : 'Perempuan' }}</td>
                    <td>{{ $p['tingkah_laku'] }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="kosong">Tiada pelajar dalam kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
