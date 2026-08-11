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
                <div class="carousel-item">
                    <img class="w-100" src="img/carousel-7.jpeg" alt="IVF care hero image">
                    <div class="carousel-caption">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-lg-8 text-start">
                                    <h1 class="display-1 text-white animated slideInRight mb-3">IVF & Fertility Care Made Clear</h1>
                                    <p class="mb-5 animated slideInRight">From fertility investigations to reproductive screening, Maraba Charity Hospital helps patients move from questions to answers with compassionate support and fast booking.</p>
                                    <a href="{{ route('appointment', ['service_id' => $featuredService?->id ?? 1]) }}" class="btn btn-primary py-3 px-5 animated slideInRight">Book fertility assessment</a>
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
                    <p class="mb-4">Skip the queues and book your diagnostic appointment online. Choose one or more services, confirm your details, and pay securely in a few steps.</p>
                    <a href="{{ route('appointment') }}" class="btn btn-primary btn-lg">Book Appointment <i class="bi bi-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Call To Action End -->


    <!-- IVF & Fertility Services Start -->
    <div class="container-fluid py-5 bg-white">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                    <div class="bg-primary rounded p-5 text-center text-white h-100 d-flex flex-column justify-content-center">
                        <i class="bi bi-heart-pulse display-1 mb-3"></i>
                        <h5 class="text-white mb-0">Compassionate Fertility Care</h5>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                    <h6 class="text-primary text-uppercase">Fertility Support</h6>
                    <h1 class="display-6 mb-3">IVF & Fertility Services</h1>
                    <p class="mb-4">In Vitro Fertilization (IVF) is a treatment that helps individuals and couples experiencing fertility challenges by combining eggs and sperm outside the body, then transferring a developing embryo to the womb. At Maraba Charity Hospital, we support patients through fertility assessment and reproductive care with clear information at every step.</p>
                    <button type="button" class="btn btn-primary py-3 px-5" data-bs-toggle="modal" data-bs-target="#ivfModal">
                        Learn More <i class="bi bi-chevron-double-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- IVF & Fertility Services End -->

    <!-- Locate Us Start -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6 wow fadeInLeft">
                    <div class="bg-white rounded shadow-sm p-4 h-100">
                        <a href="{{ route('contact') }}" class="display-6 mb-3">Locate Us</a>
                        <p class="mb-3">Maraba Charity Hospital is conveniently located in 47 Sani Abacha Rd, New Karu 900101, Federal Capital Territory, making it easy for patients to reach us for consultations, diagnostics, and fertility support.</p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Maraba Charity Hospital, Nasarawa state</li>
                            <li class="mb-2"><i class="bi bi-telephone-fill text-primary me-2"></i><span>09038301317 <span style="color:red;">|</span> 08071112717</span></li>
                            <li><i class="bi bi-envelope-fill text-primary me-2"></i>info@marabahospital.org</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInRight">
                    <div class="rounded overflow-hidden shadow-sm">
                        <iframe class="w-100" src="https://www.google.com/maps?q=9.0229824,7.5878015&z=18&output=embed" style="min-height: 260px; border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Locate Us End -->

    <!-- Gallery Start -->
<div class="container-fluid py-5 bg-white">
    <div class="container">
        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="display-6 mb-3">Our Facility</h1>
            <p class="mb-5">Take a look inside Maraba Charity Hospital – state-of-the-art equipment and a patient-friendly environment.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.1s">
                <img src="{{ asset('img/gallery-1.jpeg') }}" class="gallery-img shadow"  style="object-fit: cover" class="img-fluid rounded shadow" alt="Gallery 1">
            </div>
            <div class="col-lg-3 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.2s">
                <img src="{{ asset('img/gallery-2.jpeg') }}" class="gallery-img shadow"  style="object-fit: cover" class="img-fluid rounded shadow" alt="Gallery 2">
            </div>
            <div class="col-lg-3 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.3s">
                <img src="{{ asset('img/gallery-3.jpeg') }}" class="gallery-img shadow"  style="object-fit: cover" class="img-fluid rounded shadow" alt="Gallery 3">
            </div>
            <div class="col-lg-3 col-md-4 col-6 wow fadeInUp" data-wow-delay="0.4s">
                <img src="{{ asset('img/gallery-4.jpeg') }}" class="gallery-img shadow"  style="object-fit: cover" class="img-fluid rounded shadow" alt="Gallery 4">
            </div>
        </div>
    </div>
</div>
<!-- Gallery End -->

<style>
    .gallery-img {
    width: 100%;
    height: 350px;          /* Adjust this value as needed */
    object-fit: cover;      /* Crops images to fill the container without distortion */
    border-radius: 0.375rem; /* Match Bootstrap's rounded class */
    }
</style>

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

    <!-- IVF Modal Start -->
    <div class="modal fade" id="ivfModal" tabindex="-1" aria-labelledby="ivfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-3">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title" id="ivfModalLabel">IVF & Fertility Services</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">

                    <div class="mb-4">
                        <h5 class="text-primary mb-2"><i class="bi bi-info-circle me-2"></i>What is IVF?</h5>
                        <p class="mb-0">In Vitro Fertilization (IVF) is a fertility treatment in which an egg and sperm are combined outside the body in a laboratory setting. If fertilization occurs and an embryo develops, it may be transferred into the womb. IVF is one of several assisted reproductive approaches used to support individuals and couples on the path to pregnancy.</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-primary mb-2"><i class="bi bi-people me-2"></i>Who May Consider IVF?</h5>
                        <p class="mb-0">IVF may be considered for a range of circumstances, including certain fertility challenges, unexplained infertility, blocked or damaged fallopian tubes, male-factor infertility, or other situations identified during a fertility evaluation. Whether IVF is the right option depends on individual assessment, so a consultation is always the best starting point.</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-primary mb-2"><i class="bi bi-list-ol me-2"></i>What Does the Process Involve?</h5>
                        <ol class="mb-0 ps-3">
                            <li class="mb-2">Initial fertility assessment</li>
                            <li class="mb-2">Ovarian stimulation</li>
                            <li class="mb-2">Egg retrieval</li>
                            <li class="mb-2">Fertilization in the laboratory</li>
                            <li class="mb-2">Embryo development</li>
                            <li class="mb-2">Embryo transfer</li>
                            <li>Pregnancy testing</li>
                        </ol>
                    </div>

                    <div class="mb-2">
                        <h5 class="text-primary mb-2"><i class="bi bi-heart me-2"></i>Why Choose Our Fertility Service?</h5>
                        <p class="mb-0">We guide patients through fertility assessment and reproductive care with clear communication and individualised support at every stage — from your first consultation through follow-up. Outcomes vary from person to person, and our team is here to help you understand your specific options.</p>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <a href="{{ route('appointment', ['service_id' => $featuredService?->id ?? 1]) }}" class="btn btn-primary px-4">Book a Consultation</a>
                </div>
            </div>
        </div>
    </div>
    <!-- IVF Modal End -->
@endsection