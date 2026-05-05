@extends('layouts.report')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/' . $css . '.css') }}">
@endsection

@section('title')
    {{ $title }}
@endsection

@section('content')
    <table class="table-header-kerangka">
        <tr>
            <td>
                @if (isset($headerOne))
                    <table class="table-header">
                        {!! $headerOne !!}
                    </table>
                @endif
            </td>
            <td>
                @if (isset($headerTwo))
                    <table class="table-header">
                        {!! $headerTwo !!}
                    </table>
                @endif
            </td>
        </tr>
    </table>
    <table class="table">
        <thead class="thead-dark">
            @if (isset($headOne))
                <tr>
                    @for ($i = 0; $i < count($headOne); $i++)
                        <th rowspan="{{ $headOne[$i]['rowspan'] }}" colspan="{{ $headOne[$i]['colspan'] }}">
                            {{ $headOne[$i]['name'] }}</th>
                    @endfor
                </tr>
            @endif
            @isset($headTwo)
                <tr>
                    @for ($i = 0; $i < count($headTwo); $i++)
                        <th>{{ $headTwo[$i]['name'] }}</th>
                    @endfor
                </tr>
            @endisset
        </thead>
        <tbody>
            @for ($i = 0; $i < count($listData); $i++)
                <tr>
                    @for ($r = 0; $r < count($bodyOne); $r++)
                        <td class="{{ $bodyOne[$r]['class'] }}"
                            @if (isset($bodyOne[$r]['colspan'])) colspan="{{ $bodyOne[$r]['colspan'] }}" @endif
                            @if (isset($bodyOne[$r]['rowspan'])) rowspan="{{ $bodyOne[$r]['rowspan'] }}" @endif>
                            {{ $listData[$i][$bodyOne[$r]['field_name']] }}</td>
                    @endfor
                </tr>
                @if (isset($bodyTwo))
                    <tr>
                        @for ($r = 0; $r < count($bodyTwo); $r++)
                            <td class="{{ $bodyTwo[$r]['class'] }}">{{ $listData[$i][$bodyTwo[$r]['field_name']] }}</td>
                        @endfor
                    </tr>
                @endif
                @if (isset($bodyThree))
                    <tr>
                        @for ($r = 0; $r < count($bodyThree); $r++)
                            <td class="{{ $bodyThree[$r]['class'] }}">{{ $listData[$i][$bodyThree[$r]['field_name']] }}
                            </td>
                        @endfor
                    </tr>
                @endif
            @endfor
            @if ($title === 'Product Wise - Stock Report ( Summary )' && $principal->multi_level == 'No')
                @foreach ($stockKosong as $item)
                    <tr>
                        <td style="text-align: left">{{ $item->principal_name }}</td>
                        <td style="text-align: left">{{ $item->product_code }}</td>
                        <td style="text-align: left">{{ $item->product_name }}</td>
                        <td style="text-align: right">{{ '0' }}</td>
                        <td style="text-align: right">{{ '0' }}</td>
                        <td style="text-align: right">{{ '0' }}</td>
                        <td style="text-align: center">{{ $item->puom }}</td>
                        @if ($isVendor)
                            @for ($i = 1; $i <= 4; $i++)
                                <td style="text-align: right">
                                    {{ $item->{'ip_' . $i} ?? '' }}
                                </td>
                                <td style="text-align: right">
                                    {{ $item->{'week_' . $i} ?? '' }}
                                </td>
                            @endfor
                        @endif
                    </tr>
                @endforeach
            @endif
            <tr>
                <td class="center" colspan="{{ $columnCount }}">End Of Report</td>
            </tr>
        </tbody>
    </table>
@endsection

@section('signature')
    @if (isset($signature))
        <table class="table">
            {!! $signature !!}
        </table>
    @endif
@endsection
