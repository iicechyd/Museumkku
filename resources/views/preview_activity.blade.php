@extends('layouts.layout')
@section('title', 'จองกิจกรรมพิพิธภัณฑ์')

<head>
    <link rel="stylesheet" href="{{ asset('css/card.css') }}">
    {{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"> --}}
</head>

@section('content')
    <div class="container">
        <div class="title p-5 text-center">
            <h1 style="color: #E6A732; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
                กิจกรรมค่ายวิทยาศาสตร์
            </h1>
        </div>
    </div>
    @if ($activities->count())
        <div class="container pb-5 d-flex justify-content-center flex-nowrap gap-3">
            @foreach ($activities as $item)
                <div class="col-md-3">
                    <x-card-group>
                        <x-card title="{{ $item->activity_name }}" text="{{ $item->description }}"
                            image="{{ asset('storage/' . $item->image) }}"
                            detail="{{ route('activity_detail', ['activity_id' => $item->activity_id]) }}"
                            booking="{{ route('form_bookings.activity', ['activity_id' => $item->activity_id]) }}" />
                    </x-card-group>
                </div>
            @endforeach
        @else
            <div class="text-center pb-5">ไม่มีข้อมูลกิจกรรม</div>
        @endif
    </div>
@endsection
