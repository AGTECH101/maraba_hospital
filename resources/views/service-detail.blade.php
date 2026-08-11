@extends('layout.base')

@section('page_title', 'Service Detail')

@section('page_content')

<x-banner message="Learn more about this Service" page="Service Detail" />

<!-- ===== SERVICE DETAIL ===== -->
    <section class="service-detail-section">
        <div class="container">
            <div class="row g-5 align-items-start">
                <!-- Image / Icon -->
                <div class="col-lg-5 wow fadeInLeft" data-wow-delay="0.1s">
                    <div class="service-detail-image">
                        <i class="bi {{ $service->icon ?? 'bi-heart-pulse' }}"></i>
                    </div>
                </div>
                <!-- Content -->
                <div class="col-lg-7 wow fadeInRight" data-wow-delay="0.2s">
                    <div class="service-detail-content">
                        <h2>{{ $service->name }}</h2>
                        <p class="lead">
                            {{ $service->description }}
                        </p>
                        <p>
                            Our experienced clinical team delivers trusted laboratory support with careful reporting, patient-focused care, and dependable turnaround times.
                        </p>

                        <ul class="service-features">
                            <li><i class="bi bi-check-circle-fill"></i> Professional diagnostic support</li>
                            <li><i class="bi bi-check-circle-fill"></i> Accurate and timely reporting</li>
                            <li><i class="bi bi-check-circle-fill"></i> Expert review by qualified specialists</li>
                            <li><i class="bi bi-check-circle-fill"></i> Comfortable patient experience</li>
                        </ul>

                        <div class="service-cta-box">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h5><i class="bi bi-clock-history me-2"></i> Fast result turnaround</h5>
                                    <p class="mb-0 text-muted" style="font-size: 14px;">Reliable and confidential reporting</p>
                                </div>
                                <a href="{{ route('appointment', ['service_id' => $service->id]) }}" class="btn btn-primary">Book This Service <i class="bi bi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Service Detail End -->


    <!-- ===== OTHER SERVICES ===== -->
    <section class="other-services-section">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="section-title">Other Laboratory Services</h2>
                <p class="section-subtitle">Explore our full range of diagnostic services designed to meet diverse healthcare needs</p>
            </div>

            <div class="row g-4">
                @foreach($services as $index => $otherService)
                    @if($otherService->id !== $service->id)
                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ ($index % 4) * 0.2 + 0.1 }}s">
                            <div class="service-item">
                                <div class="icon-box-primary mb-4">
                                    <i class="bi {{ $otherService->icon ?? 'bi-heart-pulse' }} text-dark"></i>
                                </div>
                                <h5>{{ $otherService->name }}</h5>
                                <p>{{ Str::limit($otherService->description, 100) }}</p>
                                <a class="btn btn-light px-3" href="{{ route('service-detail', $otherService->slug) }}">Read More <i class="bi bi-chevron-double-right ms-1"></i></a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    <!-- Other Services End -->

@endsection