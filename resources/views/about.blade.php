@extends('layout.base')

@section('page_title', 'About Page')

@section('page_content')

<x-banner message="Get to know us better" page="About" />

<!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container">
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
                    <p class="mb-4">Maraba Hospital has been at the forefront of diagnostic medicine in Abuja for over two decades. Our commitment to excellence combines advanced laboratory technology with a team of highly trained medical professionals. We serve patients, physicians, and healthcare institutions with the highest standards of accuracy, confidentiality, and professional service.</p>
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


    <!-- Team Start -->
    <div class="container-fluid container-team py-5">
        <div class="container pb-5 d-flex gap-2">
            <div class="col-md-6 wow fadeIn" data-wow-delay="0.3s">
                <img class="img-fluid w-100" src="{{ $owner->image ?? 'img/team-1.jpg' }}" alt="{{ $owner->name ?? 'Owner' }}">
            </div>
            <div class="col-md-6 wow fadeIn" data-wow-delay="0.5s">
                <h1 class="display-6 mb-3">{{ $owner->name ?? 'Hospital Owner' }}</h1>
                <p class="mb-1">{{ $owner->specialty ?? 'CEO & Founder' }}</p>
                <p class="mb-5">Maraba Hospital, Abuja, Nigeria</p>
                <h3 class="mb-3">Biography</h3>
                <p class="mb-4">{{ $owner->bio ?? 'Biography coming soon.' }}</p>
                <div class="d-flex gap-2">
                    <a class="btn btn-lg-square btn-primary" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-lg-square btn-primary" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-lg-square btn-primary" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-lg-square btn-primary" href=""><i class="fab fa-youtube"></i></a>
                </div>
            </div>
    </div>

    <div class="row g-4">
    @forelse($staff as $index => $member)
        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ ($index % 4) * 0.2 + 0.1 }}s">
            <div class="team-item">
                <div class="position-relative overflow-hidden">
                    <img class="img-fluid w-100" src="{{ $member->image ?? 'img/team-2.jpg' }}" alt="{{ $member->name }}">
                    <div class="team-social">
                        <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                        <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="text-center p-4">
                    <h5 class="mb-1">{{ $member->name }}</h5>
                    <span>{{ ucfirst($member->role) }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center text-muted">No staff listed yet.</div>
    @endforelse
</div>
    <!-- Team End -->

@endsection