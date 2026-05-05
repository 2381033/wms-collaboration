@extends('layouts.error')

@section('title')
    Forbidden
@endsection

@section('content')    
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Forbidden</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Forbidden</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs -->

    <section id="about-us" class="about-us">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-12">
                    <h1>
                        Oops!</h1>
                    <h2>
                        403 Forbidden</h2>

                    <p>
                        You do not have permission to access the document or program that you requested.
                    </p>
                    <br>
                    <p>
                        <h2>{{ $exception->getMessage() }}</h2>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    
@endpush

{{-- @extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden')) --}}