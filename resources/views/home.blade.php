@extends('layouts.main')

@section('title')
    Home
@endsection

@section('content')
    <!-- ======= Breadcrumbs ======= -->
    {{-- <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Dashboard</h2>
                <ol>
                    <li>Home</li>
                </ol>
            </div>
        </div>
    </section> --}}
    <!-- ======= Services Section ======= -->
    <section id="hero">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-ride="carousel">
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active" style="background-image: url(images/MKT4.jpg);">
                    <div class="carousel-container">
                        <div class="carousel-content animate__animated animate__fadeInUp">
                            <h2>Welcome to <span>PT Masaji Kargosentra Tama</span></h2>
                            <h4>
                                <p>Connecting Indonesia</p>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url(images/MKT5.jpg); ">
                    <div class="carousel-container">
                        <div class="carousel-content animate__animated animate__fadeInUp">
                            <h2>Welcome to <span>PT Masaji Kargosentra Tama</span></h2>
                            <h4>
                                <p>Connecting Indonesia</p>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url(images/MKT6.jpg);">
                    <div class="carousel-container">
                        <div class="carousel-content animate__animated animate__fadeInUp">
                            <h2>Welcome to <span>PT Masaji Kargosentra Tama</span></h2>
                            <h4>
                                <p>Connecting Indonesia</p>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="carousel-item" style="background-image: url(images/MKT8.jpg);">
                    <div class="carousel-container">
                        <div class="carousel-content animate__animated animate__fadeInUp">
                            <h2>Welcome to <span>PT Masaji Kargosentra Tama</span></h2>
                            <h4>
                                <p>Connecting Indonesia</p>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon bx bx-left-arrow" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>

            <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon bx bx-right-arrow" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>

            <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
        </div>
    </section>
@endsection

@push('styles')
@endpush

@push('scripts')
@endpush
