@extends('layouts.layout_admin')
@section('title', 'เข้าชมวอคอิน')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('css/card.css') }}">
</head>

@section('content')
    <div class="container">
        <div class="title p-4 text-center">
            <h1 style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
                เข้าชมวอคอิน
            </h1>
        </div>
    </div>
    @if ($activities->count())
    <div class="container pb-5 d-flex justify-content-center flex-wrap gap-4">
        @foreach ($activities as $item)
        <div class="w-full sm:w-1/2 lg:w-1/4">
            <x-card-group>
                        <x-card 
                            title="{{ $item->activity_name }}" text="{{ $item->description }}"
                            image="{{ $item->images->isNotEmpty() ? asset('storage/' . $item->images->first()->image_path) : asset('') }}"
                            booking="{{ route('admin_bookings.activity', ['activity_id' => $item->activity_id]) }}" 
                            bookingText="จองเข้าชม"
                            hideDetail=true
                            
                            />
                    </x-card-group>
                </div>
            @endforeach
        @else
            <div class="text-center pb-5">ไม่มีข้อมูลกิจกรรม</div>
    @endif
    </div>
@endsection
</html>