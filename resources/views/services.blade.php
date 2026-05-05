@extends('layouts.main')

@section('title')
    Services
@endsection

@section('content')    
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Services</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Services</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs -->

    <!-- ======= Pricing Section ======= -->
    <section id="services" class="services">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="icofont-computer"></i></div>
                        <h4 class="title"><a href="">CFS FOR EXPORT AND IMPORT CONSOLIDATION</a></h4>
                        <p class="description">
                            We provide warehouse for LCL (Less Container Load) Cargo and do consolidation for Export-Import Shipment.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="icofont-computer"></i></div>
                        <h4 class="title"><a href="">ADDITIONAL SERVICES</a></h4>
                        <p class="description">
                            <ul>
                                <li>Inland Clearance Depot for FCL container</li>
                                <li>Pick-pack, Repacking, Rebagging, and Palletizing</li>
                                <li>Quality Inspection Facility</li>
                                <li>Garment On Hanger</li>
                                <li>Inventory Management</li>
                                <li>Warehouse Management Customs Clearance</li>
                            </ul>                            
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="icofont-computer"></i></div>
                        <h4 class="title"><a href="">CARGO DELIVERY</a></h4>
                        <p class="description">
                            We provide warehouse for LCL (Less Container Load) Cargo and do consolidation for Export-Import Shipment.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="icofont-computer"></i></div>
                        <h4 class="title"><a href="">CFS FOR EXPORT AND IMPORT CONSOLIDATION</a></h4>
                        <p class="description">
                            We provide trucking with all variant armada for delivery cargo to port, city and remote area.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="icofont-computer"></i></div>
                        <h4 class="title"><a href="">FCL & CY HANDLING</a></h4>
                        <p class="description">
                            We provide facility Container Yard for Storage Container and FCL Handling activity.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="icofont-computer"></i></div>
                        <h4 class="title"><a href="">PROJECT CARGO HANDLING</a></h4>
                        <p class="description">
                            We support activity for project cargo such as Over Weight and Over Dimension Cargo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End Pricing Section -->

@endsection

@push('scripts')
    
@endpush