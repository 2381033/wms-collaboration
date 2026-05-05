@extends('layouts.main')

@section('title')
    About Us
@endsection

@section('content')    
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>About Us</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>About Us</li>
                </ol>
            </div>
        </div>
    </section><!-- End Breadcrumbs -->

    <section id="about-us" class="about-us">
        <div class="container">
            <div class="row no-gutters">
                <div class="col-xl-5 d-flex align-items-stretch justify-content-center justify-content-lg-start" data-aos="fade-right">
                    <img src="{{asset('images/MKT8.jpg')}}" alt="" class="img-fluid">
                </div>
                <div class="col-xl-7 pl-0 pl-lg-5 pr-lg-1 d-flex align-items-stretch">
                    <div class="content d-flex flex-column justify-content-center">
                        <h3 data-aos="fade-up">At a Glance</h3>
                        <p data-aos="fade-up">
                            Masaji Kargosentra Tama (MKT) is a subsidiary of Samudera Indonesia. Warehousing is our expertise since our establishment on July 28, 1992. In 1993, we started to operate owned warehouse. Our experiences help us to understand customer’s needs by providing the best solution and integrated service for logistics chains.
                        </p>
                        <br>
                        <p data-aos="fade-up">
                            More than 25 years experience in export consolidation and import deconsolidation service has inspired us to establish collaboration and grow together with our partners. Through this partnership, our customers have obtained several benefits such as improved lead-times, single point of contact, service level enhancement, and one stop service for CFS consolidation.
                        </p>
                    </div>
                </div> 
            </div>
        </div>
    </section>

    <section id="strengths" class="strengths">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Our <strong>STRENGTHS</strong></h2>
            </div>
  
            <div class="row no-gutters clients-wrap clearfix" data-aos="fade-up">
                <div class="col-md-3 col-sm-3"></div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <ul>
                        <li>24 Hours CCTV Monitoring & Security Guard</li>
                        <li>C-TPAT & HSE Compliance</li>
                        <li>Anti Corruption And Bribery Compliance</li>
                        <li>Burglar Alarm System </li>
                        <li>Warehouse Location Close To Container Port</li>
                        <li>Qualified Manpower</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="about-us" class="about-us">
        <div class="container">
            <div class="row no-gutters">
                <div class="col-xl-5 d-flex align-items-stretch justify-content-center justify-content-lg-start" data-aos="fade-right">
                    <img src="{{asset('images/MKT5.jpg')}}" alt="" class="img-fluid">
                </div>
                <div class="col-xl-7 pl-0 pl-lg-5 pr-lg-1 d-flex align-items-stretch">
                    <div class="content d-flex flex-column justify-content-center">                        
                        <h3 data-aos="fade-up">OUR VALUE PROPOSITION</h3>
                        <div class="row">
                            <div class="col-md-6 icon-box" data-aos="fade-up">
                                <i class="bx bx-receipt"></i><h4>CLEAN</h4>
                                <p>Our warehouse implements a quality management policy in every process.</p>
                            </div>
                            <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="100">
                                <i class="bx bx-shield"></i><h4>SECURE</h4>
                                <p>Our warehouse has a strict guard and cctv monitor for 24 hours with CT-PAT standard.
                                </p>
                            </div>
                            <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="200">
                                <i class="bx bx-images"></i><h4>FAST</h4>
                                <p>Our warehouse is equipped with mechanical devices in accordance with standard warehouse handling, so that every activity can be processed quickly.
                                </p>
                            </div>
                            <div class="col-md-6 icon-box" data-aos="fade-up" data-aos-delay="300">
                                <i class="bx bx-cube-alt"></i><h4>ACCURATE</h4>
                                <p>Our warehouse records the entire movement of goods into our online system that can be a real time monitored.
                                </p>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </section>

    <section id="clients" class="clients">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Our <strong>Network</strong></h2>
            </div>
  
            <div class="row no-gutters clients-wrap clearfix" data-aos="fade-up">
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <div class="client-logo">
                        Jakarta Cikarang Bogor Bandung Medan
                    </div>
                </div>
    
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <div class="client-logo">
                        Padang Panjang Semarang Yogyakarta Makassar
                    </div>
                </div>
    
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <div class="client-logo">
                        Surabaya Bali Kendari Manado Balikpapan
                    </div>
                </div>
    
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <div class="client-logo">
                        Banjarmasin Pontianak Palembang Sorong Jayapura
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="team" class="team">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Our <strong>Teams</strong></h2>
                <p>Board Of Commissioners</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6"></div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
                    <div class="member" data-aos="fade-up">
                        <div class="member-img">
                            <img src="{{asset('images/teams/tigor.jpg')}}" class="img-fluid" alt="">
                            <div class="social">
                                <a href=""><i class="icofont-twitter"></i></a>
                                <a href=""><i class="icofont-facebook"></i></a>
                                <a href=""><i class="icofont-instagram"></i></a>
                                <a href=""><i class="icofont-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Tigor A. Hakim</h4>
                            <span>Komisaris</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
                    <div class="member" data-aos="fade-up">
                        <div class="member-img">
                            <img src="{{asset('images/teams/bani.jpg')}}" class="img-fluid" alt="">
                            <div class="social">
                                <a href=""><i class="icofont-twitter"></i></a>
                                <a href=""><i class="icofont-facebook"></i></a>
                                <a href=""><i class="icofont-instagram"></i></a>
                                <a href=""><i class="icofont-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Bani M. Mulia</h4>
                            <span>Komisaris Utama</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="team" class="team">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Our <strong>Teams</strong></h2>
                <p>Board Of Directors</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
                    <div class="member" data-aos="fade-up">
                        <div class="member-img">
                            <img src="{{asset('images/teams/nandan.jpg')}}" class="img-fluid" alt="">
                            <div class="social">
                                <a href=""><i class="icofont-twitter"></i></a>
                                <a href=""><i class="icofont-facebook"></i></a>
                                <a href=""><i class="icofont-instagram"></i></a>
                                <a href=""><i class="icofont-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Nandan Firdaus</h4>
                            <span>Direktur Pengembangan Usaha</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
                    <div class="member" data-aos="fade-up">
                        <div class="member-img">
                            <img src="{{asset('images/teams/rima.jpg')}}" class="img-fluid" alt="">
                            <div class="social">
                                <a href=""><i class="icofont-twitter"></i></a>
                                <a href=""><i class="icofont-facebook"></i></a>
                                <a href=""><i class="icofont-instagram"></i></a>
                                <a href=""><i class="icofont-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Rima Joko Dwifaryuni</h4>
                            <span>Direktur Keuangan</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
                    <div class="member" data-aos="fade-up">
                        <div class="member-img">
                            <img src="{{asset('images/teams/prabowo.jpg')}}" class="img-fluid" alt="">
                            <div class="social">
                                <a href=""><i class="icofont-twitter"></i></a>
                                <a href=""><i class="icofont-facebook"></i></a>
                                <a href=""><i class="icofont-instagram"></i></a>
                                <a href=""><i class="icofont-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Prabowo Budhy Santoso</h4>
                            <span>Direktur Utama</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex align-items-stretch">
                    <div class="member" data-aos="fade-up">
                        <div class="member-img">
                            <img src="{{asset('images/teams/andreana.jpg')}}" class="img-fluid" alt="">
                            <div class="social">
                                <a href=""><i class="icofont-twitter"></i></a>
                                <a href=""><i class="icofont-facebook"></i></a>
                                <a href=""><i class="icofont-instagram"></i></a>
                                <a href=""><i class="icofont-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="member-info">
                            <h4>Andreana Yunizar</h4>
                            <span>Direktur Pengelola</span>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </section>
@endsection

@section('modal')

@endsection

@push('scripts')
    
@endpush