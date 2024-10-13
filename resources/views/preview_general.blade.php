@extends('layouts.layout')
@section('title', 'จองกิจกรรมพิพิธภัณฑ์')

<head>
    <link rel="stylesheet" href="{{ asset('css/card.css') }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

@section('content')
    <div class="container">
        <div class="title p-5 text-center">
            <h1 style="color: #489085; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">
                จองเข้าชมพิพิธภัณฑ์
            </h1>
        </div>
    </div>
    @if ($activities->count())
        <div class="container pb-5 d-flex flex-wrap justify-content-center">
            @foreach ($activities as $item)
                <div class="col-md-3 mb-4">
                    <x-card-group>
                        <x-card title="{{ $item->activity_name }}" text="{{ $item->description }}"
                            image="{{ asset('images/' . $item->image) }}"
                            detail="{{ route('activity_detail', ['activity_id' => $item->activity_id]) }}"
                            booking="{{ route('form_bookings.activity', ['activity_id' => $item->activity_id]) }}" />
                    </x-card-group>
                </div>
            @endforeach
        @else
            <div class="text-center pb-5">ไม่มีข้อมูลกิจกรรม</div>
    @endif
    </div>
    <!-- Modal -->
    <div class="modal fade" id="activityDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p></p>
                    <img src="" class="img-fluid" alt="Activity Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
@endsection
