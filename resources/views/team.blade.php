@extends('layout.base')

@section('page_title', 'Our Team')

@section('page_content')

<x-banner message="Our Team of specialists" page="Our Team" />

<!-- Team Start -->
    <div class="container-fluid container-team py-5">
        <div class="container pb-5">
            @php $featured = $owner; @endphp
            @if($featured)
                <div class="row g-5 align-items-center mb-5">
                    <div class="col-md-6 wow fadeIn" data-wow-delay="0.3s">
                        <img class="img-fluid w-100" src="{{ $featured->image ?? 'img/team-1.jpg' }}" alt="{{ $featured->name }}">
                    </div>
                    <div class="col-md-6 wow fadeIn" data-wow-delay="0.5s">
                        <h1 class="display-6 mb-3">{{ $featured->name }}</h1>
                        <p class="mb-1">{{ $featured->specialty ?? $featured->role }}</p>
                        <p class="mb-5">{{ $featured->email }}</p>
                        <h3 class="mb-3">Biography</h3>
                        <p class="mb-4">{{ $featured->bio ?: 'This team member is part of our growing network of professionals dedicated to providing trusted diagnostic care.' }}</p>
                        <div class="d-flex">
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-linkedin-in"></i></a>
                            <a class="btn btn-lg-square btn-primary me-2" href=""><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row g-4">
                @foreach($staff as $index => $member)
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ ($index % 4) * 0.2 + 0.1 }}s">
                        <div class="team-item">
                            <div class="position-relative overflow-hidden">
                                <img class="img-fluid w-100" src="{{ $member->image ?? 'img/team-1.jpg' }}" alt="{{ $member->name }}">
                                <div class="team-social">
                                    <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-linkedin-in"></i></a>
                                    <a class="btn btn-square btn-light mx-1" href=""><i class="fab fa-youtube"></i></a>
                                </div>
                            </div>
                            <div class="text-center p-4">
                                <h5 class="mb-1">{{ $member->name }}</h5>
                                <span>{{ $member->specialty ?? $member->role }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Team End -->

@endsection