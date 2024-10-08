@props(['title', 'text', 'image', 'footer'])


<div class="card" style="width: 18rem; min-height: 100%; display: flex; flex-direction: column;">
    <img src="{{ $image }}" class="card-img-top card-img " style="border-radius: 0;" alt="card-img">
    <div class="card-body">
        <h5 class="card-title">{{ $title }}</h5>
        <p class="card-text">{{ $text }}</p>
    </div>
    <div class="footer text-center" style="padding: 10px;">
        <a href="#" class="btn text-white"
            style="width: 100%; margin: 0 auto; background-color: #489085;">รายละเอียดเพิ่มเติม</a>
    </div>
</div>
