@extends('layouts.report')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
@endsection

@section('title')
    {{ 'Tracking Carton Report' }}
    <div style="display: flex; justify-content: flex-end;">
        <form method="POST" action="{{ route('exportBySku') }}" class="no-print">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $array['branch_id'] }}">
            <input type="hidden" name="principal_id" value="{{ $array['principal_id'] }}">
            <input type="hidden" name="type" value="{{ $array['type'] }}">
            @foreach ($array['product_code'] as $code)
                <input type="hidden" name="product_code[]" value="{{ $code }}">
            @endforeach
            <button type="submit"
                style="background-color: green; color: azure; width: 150px; height: 50px; font-size: 16px;">
                Export To Excel
            </button>
        </form>
    </div>
@endsection

@section('content')
    <table class="table-header-kerangka">
    </table>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th colspan="7">INBOUND TRANSACTION</th>
            </tr>
            <tr>
                {{-- <th>JOB NO</th> --}}
                <th>INBOUND DATE</th>
                <th>VEHICLE NO</th>
                <th>DESCRIPTION</th>
                <th>PRODUCT CODE</th>
                <th>QTY</th>
                <th>CARTON ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groupedEanCodes as $jobNo => $productsByJob)
                @foreach ($productsByJob as $productCode => $data)
                    @php
                        $ean_index = 1;
                    @endphp
                    <tr>
                        {{-- <td>{{ $data['job_no'] }}</td> --}}
                        <td>{{ $data['created_at'] }}</td>
                        <td>{{ $data['vehicle'] }}</td>
                        <td>{{ $data['deskripsi'] }}</td>
                        <td>{{ $data['product_code'] }}</td>
                        <td>{{ $data['ean_count'] . ' ' . $data['puom'] }}</td>
                        <td style="min-width: 150px;">
                            <ol start="{{ $ean_index }}"
                                style="max-height: 150px; overflow-y: auto; padding-left: 20px; list-style-position: inside;">
                                @foreach ($data['ean_codes'] as $ean)
                                    <li>{{ $ean }}</li>
                                    @php $ean_index++; @endphp
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th colspan="8">OUTBOUND TRANSACTION</th>
            </tr>
            <tr>
                {{-- <th>JOB NO</th> --}}
                <th>JOB DATE</th>
                <th>VEHICLE NO</th>
                <th>CUSTOMER NAME</th>
                <th>PRODUCT CODE</th>
                <th>QTY</th>
                <th>CARTON ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groupedOutbound as $group)
                @foreach ($group['products'] as $product)
                    @php
                        $eanIndex = 1;
                    @endphp
                    <tr>
                        {{-- <td>{{ $group['job_no'] }}</td> --}}
                        <td>{{ $group['job_date'] }}</td>
                        <td>{{ $group['vehicle_no'] }}</td>
                        <td>{{ $group['customer_name'] }}</td>
                        <td>{{ $product['product_code'] }}</td>
                        <td>{{ $product['ean_code_count'] . ' ' . $product['puom'] }}</td>
                        <td>
                            @if ($product['ean_codes']->isNotEmpty())
                                <ol start="{{ $eanIndex }}"
                                    style="max-height: 150px; overflow-y: auto; padding-left: 20px; list-style-position: inside;">
                                    @foreach ($product['ean_codes'] as $ean)
                                        <li>{{ $ean }}</li>
                                        @php $eanIndex++; @endphp
                                    @endforeach
                                </ol>
                            @else
                                <em>-</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endsection
