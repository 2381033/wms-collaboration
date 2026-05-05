@extends('layouts.report')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/landscape.css') }}">
@endsection

@section('title')
    {{ 'Tracking Carton Report' }}

    <div style="display: flex; justify-content: flex-end;">
        <form method="POST" action="{{ route('exportByCarton') }}" class="no-print">
            @csrf
            @foreach ($cartonID as $value)
                <input type="hidden" name="carton_id[]" value="{{ $value }}">
            @endforeach
            <button type="submit"
                style="background-color: green; color: azure; width: 150px; height: 50px; font-size: 16px;">
                Export To Excel
            </button>
        </form>
    </div>
@endsection

@section('content')
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th colspan="7">INBOUND TRANSACTION</th>
            </tr>
            <tr>
                <th>JOB NO</th>
                <th>INBOUND DATE</th>
                <th>VEHICLE NO</th>
                <th>DESCRIPTION</th>
                <th>PRODUCT CODE</th>
                <th>CARTON ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredInbound as $value)
                <tr>
                    <td>{{ $value->job_no }}</td>
                    <td>{{ $value->confirmed_date }}</td>
                    <td>{{ $value->vehicle_no }}</td>
                    <td>{{ $value->description }}</td>
                    <td>{{ $value->product_code }}</td>
                    <td>{{ $value->ean_code }}</td>
                </tr>
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
                <th>JOB NO</th>
                <th>JOB DATE</th>
                <th>VEHICLE NO</th>
                <th>CUSTOMER NAME</th>
                <th>PRODUCT CODE</th>
                <th>CARTON ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredOutbound as $group)
                <tr>
                    <td>{{ $group->job_no }}</td>
                    <td>{{ $group->confirmed_date }}</td>
                    <td>{{ $group->vehicle_no }}</td>
                    <td>{{ $group->customer_name }}</td>
                    <td>{{ $group->product_code }}</td>
                    <td>{{ $group->ean_code }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
