@extends('layout.base')

@section('page_title', 'Home Page')

@section('page_content')
        <!-- Carousel Start -->
    <div class="container-fluid header-carousel px-0 mb-5">
        <div id="header-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/carousel-2.jpeg" alt="Image">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-lg-7 text-start">
                                    <h1 class="display-1 text-white animated slideInRight mb-3">Award Winning Laboratory Center</h1>
                                    <p class="mb-5 animated slideInRight">Experience comprehensive diagnostic testing with state-of-the-art technology and highly qualified medical professionals dedicated to your health and wellness at Maraba Charity Hospital.</p>
                                    <a href="" class="btn btn-primary py-3 px-5 animated slideInRight">Explore More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/carousel-4.jpeg" alt="Image">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row justify-content-end">
                                <div class="col-lg-7 text-end">
                                    <h1 class="display-1 text-white animated slideInLeft mb-3">Expert Doctors & Lab Assistants</h1>
                                    <p class="mb-5 animated slideInLeft">Our team of certified laboratory professionals and experienced technicians ensures accurate results and exceptional patient care for every test we perform.</p>
                                    <a href="" class="btn btn-primary py-3 px-5 animated slideInLeft">Explore More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container pb-5">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="row g-0">
                        <div class="col-6">
                            <img class="img-fluid" src="img/about-1.jpeg">
                        </div>
                        <div class="col-6">
                            <img class="img-fluid" src="img/about-2.jpeg">
                        </div>
                        <div class="col-6">
                            <img class="img-fluid" src="img/about-3.jpeg">
                        </div>
                        <div class="col-6">
                            <div class="bg-primary w-100 h-100 mt-n5 ms-n5 d-flex flex-column align-items-center justify-content-center">
                                <div class="icon-box-light mt-4">
                                    <i class="bi bi-award text-dark"></i>
                                </div>
                                <h1 class="display-1 text-white mb-0" data-toggle="counter-up">25</h1>
                                <small class="fs-5 text-white mb-4">Years Experience</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="display-6 mb-4">Trusted Lab Experts and Latest Lab Technologies</h1>
                    <p class="mb-4">Maraba Charity Hospital combines cutting-edge laboratory equipment with the expertise of our dedicated pathologists and technicians to deliver precise diagnostic results. With over 25 years of experience serving the Abuja community, we've earned the trust of thousands of patients and healthcare providers through our commitment to accuracy, reliability, and excellence.</p>
                    <div class="row g-4 g-sm-5 justify-content-center">
                        <div class="col-sm-6">
                            <div class="about-fact btn-square flex-column rounded-circle bg-primary ms-sm-auto">
                                <p class="text-white mb-0">Awards Winning</p>
                                <h1 class="text-white mb-0" data-toggle="counter-up">9999</h1>
                            </div>
                        </div>
                        <div class="col-sm-6 text-start">
                            <div class="about-fact btn-square flex-column rounded-circle bg-secondary me-sm-auto">
                                <p class="text-white mb-0">Complete Cases</p>
                                <h1 class="text-white mb-0" data-toggle="counter-up">9999</h1>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="about-fact mt-n130 btn-square flex-column rounded-circle bg-dark mx-sm-auto">
                                <p class="text-white mb-0">Happy Clients</p>
                                <h1 class="text-white mb-0" data-toggle="counter-up">9999</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Service Start -->
    <div class="container-fluid container-service py-5">
        <div class="container pt-5">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="display-6 mb-3">Reliable & High-Quality Laboratory Service</h1>
                <p class="mb-5">We provide comprehensive diagnostic testing services with rapid turnaround times, competitive pricing, and results you can trust. Our laboratory is equipped with modern technology and staffed by certified professionals.</p>
            </div>
            <div class="row g-4">
                @forelse($services as $index => $service)
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ ($index % 4) * 0.2 + 0.1 }}s">
                        <div class="service-item">
                            <div class="icon-box-primary mb-4">
                                <i class="bi {{ $service->icon ?? 'bi-heart-pulse' }} text-dark"></i>
                            </div>
                            <h5 class="mb-3">{{ $service->name }}</h5>
                            <p class="mb-4">{{ Str::limit($service->description, 120) }}</p>
                            <a class="btn btn-light px-3" href="{{ route('service-detail', $service->slug) }}">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">No services available yet.</div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Service End -->


    <!-- Call To Action Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 wow fadeInLeft">
                    <img src="img/appointment.webp" class="img-fluid rounded shadow" alt="Book appointment">
                </div>
                <div class="col-md-6 wow fadeInRight">
                    <h2 class="display-6">Book an Appointment Easily</h2>
                    <p class="mb-4">Skip the queues and book your diagnostic appointment online. Choose the service you need, pick a specialist, and confirm your slot — fast and secure.</p>
                    <a href="{{ route('appointment') }}" class="btn btn-primary btn-lg">Book Appointment <i class="bi bi-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Call To Action End -->


    <!-- Team Start -->
    <div class="container-fluid container-team py-5">
        <div class="container pb-5">
            <div class="row g-5 align-items-center mb-5">
                <div class="col-md-6 wow fadeIn" data-wow-delay="0.3s">
                    <img class="img-fluid w-100" src="{{ $owner->image ?? 'img/team-1.jpg' }}" alt="{{ $owner->name ?? 'Owner' }}">
                </div>
                <div class="col-md-6 wow fadeIn" data-wow-delay="0.5s">
                    <h1 class="display-6 mb-3">{{ $owner->name ?? 'Hospital Owner' }} <span class="badge bg-warning text-dark ms-2">Owner</span></h1>
                    <p class="mb-1">{{ $owner->specialty ?? 'CEO & Founder' }}</p>
                    <p class="mb-5">Maraba Charity Hospital, Abuja, Nigeria</p>
                    <h3 class="mb-3">Biography</h3>
                    <p class="mb-4">{{ $owner->bio ?? 'Biography coming soon.' }}</p>
                        <div class="d-flex gap-2">
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-linkedin-in"></i></a>
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 m-4">
                @forelse($staff as $s)
                    <div class="col-lg-3 col-md-6 wow fadeInUp">
                        <div class="team-item">
                            <div class="position-relative overflow-hidden">
                                <img class="img-fluid w-100" src="{{ $s->image ?? 'img/team-2.jpg' }}" alt="{{ $s->name }}">
                                <div class="team-social">
                                                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-facebook-f"></i></a>
                                                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-twitter"></i></a>
                                                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-linkedin-in"></i></a>
                                                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-youtube"></i></a>
                                </div>
                            </div>
                            <div class="text-center p-4">
                                <h5 class="mb-1">{{ $s->name }} @if($s->role === 'owner')<span class="badge bg-warning text-dark ms-2">Owner</span>@endif</h5>
                                <span>{{ ucfirst($s->role) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">No staff listed yet.</div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Team End -->
@endsection