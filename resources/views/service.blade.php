@extends('layout.base')

@section('page_title', 'Services')

@section('page_content')

<x-banner message="A clue of services we offer" page="Services" />

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


@endsection

{{-- Add this CSS to your main stylesheet or inside a <style> block --}}
@push('styles')
<style>
    .service-description {
        display: -webkit-box;
        -webkit-line-clamp: 3;          /* Limit to 3 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
        line-height: 1.5;               /* Adjust as needed */
        max-height: calc(1.5em * 3);    /* Fallback for older browsers */
    }
    .service-item {
        height: 100%;                   /* Ensures all cards stretch to same height */
    }
</style>
@endpush