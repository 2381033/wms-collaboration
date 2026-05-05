@extends('layouts.error')

@section('title')
    Not Found
@endsection

@section('content')    
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Not Found</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Not Found</li>
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
                        404 Not Found</h2>

                    <p>
                        The page you were looking for could not be found.
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