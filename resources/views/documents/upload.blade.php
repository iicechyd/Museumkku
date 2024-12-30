@extends('layouts.layout')
@section('title', 'อัปโหลดไฟล์ขอความอนุเคราะห์')
@section('content')

<div class="container mt-5">
    <h1 class="text-center mb-4">อัปโหลดเอกสาร</h1>
    <form action="{{ route('documents.store', $booking->booking_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="document" class="form-label">อัปโหลดเอกสาร (PDF เท่านั้น):</label>
            <input type="file" name="document" accept=".pdf" id="document" class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">อัปโหลด</button>
        </div>
    </form>
</div>
@endsection