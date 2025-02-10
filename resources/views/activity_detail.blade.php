<!DOCTYPE html>
<html lang="en">
@extends('layouts.layout')
@section('title', 'จองกิจกรรมพิพิธภัณฑ์')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/activity_detail.css') }}">
</head>

@section('sidebar')
@section('content')
    <div class="container py-5">
        <div class="srow gy-4">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="row g-0">
                        <div class="col-lg-6 col-md-12 px-3 py-2 ">
                            <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach ($activity->images as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <div class="image"
                                                style="width: 602px; height: 400px; display: flex; justify-content: center; align-items: center; overflow: hidden; cursor: pointer; border-radius: 3px;"
                                                onclick="showLargeImage('{{ asset('storage/' . $image->image_path) }}')">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                    class="d-block w-100"
                                                    style="max-height: 100%; max-width: 100%;  contain; "
                                                    alt="Image for {{ $activity->activity_name }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Controls -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                                <div id="carouselIndicator" class="carousel-indicator">1/{{ count($activity->images) }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 py-2 pb-3">
                                @foreach ($activity->images->take(4) as $image)
                                    <div class="position-relative">
                                        <div class="bg-primary text-white text-center d-flex align-items-center justify-content-center"
                                            style="width: 114px; height: 100px; border-radius: 3px; overflow: hidden; cursor: pointer;"
                                            onclick="showLargeImage('{{ asset('storage/' . $image->image_path) }}')">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Activity Image"
                                                class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                        </div>
                                    </div>
                                @endforeach
                            
                                @if ($activity->images->count() > 5)
                                    <div class="position-relative">
                                        <div class="bg-primary text-white text-center d-flex align-items-center justify-content-center"
                                            style="width: 114px; height: 100px; border-radius: 3px; overflow: hidden; cursor: pointer;"
                                            onclick="openGalleryModal()">
                                            <img src="{{ asset('storage/' . $activity->images[4]->image_path) }}" alt="Activity Image"
                                                class="img-fluid" style="object-fit: cover; width: 100%; height: 100%; filter: brightness(0.6);">
                                            <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center"
                                                style="top: 0; left: 0; background: rgba(0, 0, 0, 0.5); font-size: 20px;">
                                                +{{ $activity->images->count() - 5 }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($activity->images->count() == 5)
                                    <div class="position-relative">
                                        <div class="bg-primary text-white text-center d-flex align-items-center justify-content-center"
                                            style="width: 114px; height: 100px; border-radius: 3px; overflow: hidden; cursor: pointer;"
                                            onclick="showLargeImage('{{ asset('storage/' . $activity->images[4]->image_path) }}')">
                                            <img src="{{ asset('storage/' . $activity->images[4]->image_path) }}" alt="Activity Image"
                                                class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="card-body">
                                <h2 class="card-title">{{ $activity->activity_name }}</h2>
                                <p class="card-text text-muted">{{ $activity->description }}</p>
                                <div class="mt-3">
                                    <p>ราคาเข้าชม</p>
                                    <p>
                                        เด็ก ( 3 ขวบ - ประถม ) :
                                        {{ $activity->children_price > 0 ? $activity->children_price . ' บาท/คน' : 'ฟรี' }}
                                        |
                                        นักเรียนมัธยม/นักศึกษา :
                                        {{ $activity->student_price > 0 ? $activity->student_price . ' บาท/คน' : 'ฟรี' }} |
                                        ผู้ใหญ่ :
                                        {{ $activity->adult_price > 0 ? $activity->adult_price . ' บาท/คน' : 'ฟรี' }}
                                    </p>
                                    <p>สวัสดิการ</p>
                                    <p>
                                        เด็กเล็ก ( ต่ำกว่า 3 ขวบ )
                                        |
                                        ผู้พิการ
                                        |
                                        ผู้สูงอายุ
                                        |
                                        พระภิกษุสงฆ์ /สามเณร
                                    </p>
                                </div>
                                <p class="custom-gray-text">
                                    @if ($activity->max_capacity)
                                        <span>จำกัดจำนวนผู้เข้าชมไม่เกิน {{ $activity->max_capacity }} คน และ ไม่ต่ำกว่า 50
                                            คนต่อการจอง</span>
                                        <span class="new-line">(หากผู้เข้าชมเกิน {{ $activity->max_capacity }} คน
                                            กรุณาติดต่อเจ้าหน้าที่ 0XX-XXXX )</span>
                                    @else
                                        <span>ไม่จำกัดจำนวนผู้เข้าชม และ ไม่ต่ำกว่า 50 คนต่อการจอง</span>
                                    @endif
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('form_bookings.activity', ['activity_id' => $activity->activity_id]) }}"
                                        class="btn text-white width:50%"
                                        style="background-color: #489085; font-family: 'Noto Sans Thai', sans-serif;">
                                        จองกิจกรรม
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal สำหรับแสดงภาพทั้งหมด -->
                        <div id="galleryModal" class="modal fade" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                                            @foreach ($activity->images->slice(5) as $image)
                                                <div class="position-relative">
                                                    <div class="bg-primary text-white text-center d-flex align-items-center justify-content-center"
                                                        style="width: 250px; height: 200px; border-radius: 3px; overflow: hidden; cursor: pointer;"
                                                        onclick="showLargeImage('{{ asset('storage/' . $image->image_path) }}')">
                                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Activity Image"
                                                            class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="largeImageContainer" onclick="closeLargeImage()">
                            <img id="largeImage" src="" alt="Large Image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showLargeImage(imageUrl) {
            var largeImageContainer = document.getElementById('largeImageContainer');
            var largeImage = document.getElementById('largeImage');
            largeImage.src = imageUrl;
            largeImageContainer.style.display = 'flex';
        }

        function closeLargeImage() {
            var largeImageContainer = document.getElementById('largeImageContainer');
            largeImageContainer.style.display = 'none';
        }
        var carousel = document.getElementById('imageCarousel');
        var carouselIndicator = document.getElementById('carouselIndicator');
        carousel.addEventListener('slid.bs.carousel', function(event) {
            var currentIndex = event.to + 1;
            var totalImages = {{ count($activity->images) }};
            carouselIndicator.textContent = currentIndex + '/' + totalImages;
        });
        function openGalleryModal() {
        var galleryModal = new bootstrap.Modal(document.getElementById('galleryModal'));
        galleryModal.show();
    }
    </script>
@endsection
</html>