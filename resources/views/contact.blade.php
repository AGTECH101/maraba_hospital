@extends('layout.base')

@section('page_title', 'Contact Us')

@section('page_content')

<x-banner message="Reach out to us" page="Contact" />

<!-- Contact Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="display-6 mb-3">Have Any Query? Feel Free To Contact Us</h1>
            <p class="mb-5">Our friendly staff is ready to assist you with any questions about our laboratory services, test results, specimen collection, or general inquiries. Contact us today for reliable diagnostic solutions.</p>
        </div>
        <div class="row contact-info position-relative g-0 mb-5">
            <div class="col-lg-6">
                <a href="tel:+0123456789" class="d-flex justify-content-lg-center bg-primary p-4">
                    <div class="icon-box-light shrink-0">
                        <i class="bi bi-phone text-dark"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="text-white">Call Us</h5>
                        <h2 class="text-white mb-0"><span>09038301317 <span style="color:red;">|</span> 08071112717</span></h2>
                    </div>
                </a>
            </div>
            <div class="col-lg-6">
                <a href="mailto:info@example.com" class="d-flex justify-content-lg-center bg-primary p-4">
                    <div class="icon-box-light shrink-0">
                        <i class="bi bi-envelope text-dark"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="text-white">Mail Us</h5>
                        <h2 class="text-white mb-0">info@example.com</h2>
                    </div>
                </a>
            </div>
        </div>
    </div> <!-- close inner container -->
</div> <!-- close outer container-fluid -->
<!-- Contact End -->

<!-- Locate Us Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 wow fadeInLeft">
                <div class="bg-white rounded shadow-sm p-4 h-100">
                    <h2 class="display-6 mb-3">Locate Us</h2>
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
                    <iframe class="w-100" src="https://www.google.com/maps?ll=9.018852437231173,7.58492559389574&hl=en&z=18&output=embed" style="min-height: 260px; border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

@endsection