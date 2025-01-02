@extends('layouts.layout_admin')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/closed_date.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    @php
        use Carbon\Carbon;
        Carbon::setLocale('th');
    @endphp
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="container mx-auto px-4 py-6">
        <h1 class="table-heading text-center">จัดการวันปิดรอบการเข้าชม</h1>
        <div class="card shadow p-4">
            <form action="{{ route('admin.saveClosedDates') }}" class="row g-3" method="POST">
                @csrf
                <div class="form-group col-4">
                    <label for="activity_id" class="font-weight-bold">เลือกกิจกรรม</label>
                    <select id="activity_id" name="activity_id" class="form-control" required>
                        <option value="">กรุณาเลือกกิจกรรม</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->activity_id }}">{{ $activity->activity_name }}</option>
                        @endforeach
                    </select>
                    @error('activity_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group col-4">
                    <label for="timeslots_id" class="font-weight-bold">เลือกรอบการเข้าชม</label>
                    <select id="timeslots_id" name="timeslots_id" class="form-control" required disabled>
                        <option value="">กรุณาเลือกรอบการเข้าชม</option>
                        <option value="all">ปิดทุกรอบ</option>
                    </select>
                    @error('timeslots_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group col-4">
                    <label for="closed_on" class="font-weight-bold">วันที่ปิดรอบการเข้าชม</label>
                    <input type="date" id="closed_on" name="closed_on" class="form-control" required>
                    @error('closed_on')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary w-40 px-5">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
    <div class="container mx-auto px-4 py-6">
        <h2 class="font-weight-bold mt-5">รายการวันที่ปิด</h2>
        <div class="table-wrapper">
        <table class="table  table-bordered mt-4 shadow-sm">
            <thead class="thead-light">
                <tr>
                    <th>กิจกรรม</th>
                    <th>รอบเวลา</th>
                    <th>วันที่ปิด</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($closedDates as $closed)
                    <tr>
                        <td>{{ $closed->activity->activity_name }}</td>
                        <td>
                            @if ($closed->timeslot)
                                {{ Carbon::parse($closed->timeslot->start_time)->format('H:i') }} น. -
                                {{ Carbon::parse($closed->timeslot->end_time)->format('H:i') }} น.
                            @else
                                ปิดทุกรอบ
                            @endif
                        </td>
                        <td>{{ Carbon::parse($closed->closed_on)->translatedFormat('d /M/ ') }}{{ Carbon::parse($closed->closed_on)->year + 543 }}
                        </td>
                        <td>
                            <form action="{{ route('admin.deleteClosedDate', $closed->closed_timeslots_id) }}"
                                method="POST" onsubmit="return confirm('ยืนยันการลบวันที่ปิดรอบนี้หรือไม่?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    </div>
    <script>
        document.getElementById('activity_id').addEventListener('change', function() {
            const activityId = this.value;
            const timeslotsDropdown = document.getElementById('timeslots_id');

            if (activityId) {
                fetch("{{ route('admin.getTimeslots') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            activity_id: activityId
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        timeslotsDropdown.innerHTML = '<option value="">-- เลือกรอบการเข้าชม --</option>';
                        timeslotsDropdown.innerHTML += '<option value="all">ปิดทุกรอบ</option>';
                        data.forEach(timeslot => {
                            timeslotsDropdown.innerHTML +=
                                `<option value="${timeslot.timeslots_id}">${timeslot.start_time} - ${timeslot.end_time}</option>`;
                        });
                        timeslotsDropdown.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('ไม่สามารถโหลดข้อมูลรอบการเข้าชมได้');
                    });
            } else {
                timeslotsDropdown.innerHTML = '<option value="">-- เลือกรอบการเข้าชม --</option>';
                timeslotsDropdown.disabled = true;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    @endsection
