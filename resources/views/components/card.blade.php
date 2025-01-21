@props(['title', 'text', 'image', 'detail', 'booking'])

<div class="card" style="min-height: 29rem; display: flex; flex-direction: column;">
    <img src="{{ $image }}" class="card-img-top card-img "
        style="border-radius: 0; max-height: 100%; max-width: 100%; object-fit: cover;" alt="card-img">
    <div class="card-body">
        <h5 class="card-title">{{ $title }}</h5>
        <p class="card-text">{{ $text }}</p>
    </div>
    <div class="footer text-center" style="padding: 10px;">
        <div class="flex justify-center space-x-4">
            <a href="{{ $detail }}" class="btn text-white width:50%" style="background-color: #489085;" data-id="detail-button">
                รายละเอียดเพิ่มเติม
            </a>
            <a href="{{ $booking }}" class="btn text-white width:50%" style=" background-color: #E6A732;" data-id="booking-button">
                จองกิจกรรม
            </a>
        </div>
    </div>
</div>
