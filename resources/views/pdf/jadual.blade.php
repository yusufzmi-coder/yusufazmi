@extends('pdf.layout')

@section('kandungan')
    <table>
        <thead>
            <tr>
                <th style="width: 14%">Masa</th>
                @foreach ($grid['days'] as $day)
                    <th>{{ $day['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($grid['rows'] as $row)
                <tr>
                    <td class="kecil">{{ $row['label'] }}</td>

                    @if ($row['is_break'])
                        <td class="rehat" colspan="{{ count($grid['days']) }}">REHAT</td>
                    @else
                        @foreach ($row['cells'] as $cell)
                            <td>
                                @if ($cell['kelas'])
                                    <strong>{{ $cell['kelas']['nama'] }}</strong><br>
                                    <span class="kecil">
                                        {{ $cell['kelas']['guru'] ?? 'Tiada guru' }}<br>
                                        {{ $cell['kelas']['bilik'] ?? 'Tiada bilik' }} ·
                                        {{ $cell['kelas']['pelajar'] }} pelajar
                                    </span>
                                @else
                                    <span class="kosong">Kosong</span>
                                @endif
                            </td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
