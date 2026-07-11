@extends('layout.base')

@section('page_title', 'Services')

@section('page_content')

<x-banner message="A clue of services we offer" page="Services" />

    <!-- Service Start -->
    <div class="container-fluid container-service py-5">
        <div class="container py-5">
            <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="display-6 mb-3">Reliable & High-Quality Laboratory Service</h1>
                <p class="mb-5">Maraba Hospital offers a comprehensive range of diagnostic laboratory services designed to meet the needs of patients, healthcare professionals, and institutions. Our services are backed by modern technology, quality assurance protocols, and experienced medical professionals.</p>
            </div>
            <div class="row g-4">
                @foreach($services as $index => $service)
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ ($index % 4) * 0.1 + 0.1 }}s">
                        <div class="service-item">
                            <div class="icon-box-primary mb-4">
                                <i class="bi {{ $service->icon ?? 'bi-heart-pulse' }} text-dark"></i>
                            </div>
                            <h5 class="mb-3">{{ $service->name }}</h5>
                            <p class="mb-4">{{ $service->description }}</p>
                            <a class="btn btn-light px-3" href="{{ route('service-detail', $service->slug) }}">Read More<i class="bi bi-chevron-double-right ms-1"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Service End -->

@endsection