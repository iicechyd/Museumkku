@extends('layouts.layout_admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">จัดการวันปิดรอบการเข้าชม</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.saveClosedDates') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        <div class="mb-4">
            <label for="activity_id" class="block text-sm font-medium text-gray-700">เลือกกิจกรรม</label>
            <select id="activity_id" name="activity_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- เลือกกิจกรรม --</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->activity_id }}">{{ $activity->activity_name }}</option>
                @endforeach
            </select>
            @error('activity_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="timeslots_id" class="block text-sm font-medium text-gray-700">เลือกรอบการเข้าชม</label>
            <select id="timeslots_id" name="timeslots_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" disabled>
                <option value="">-- เลือกรอบการเข้าชม --</option>
                <option value="all">ปิดทุกรอบ</option>
            </select>
            @error('timeslots_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="closed_on" class="block text-sm font-medium text-gray-700">วันที่ปิดรอบการเข้าชม</label>
            <input type="date" id="closed_on" name="closed_on" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('closed_on')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-right">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">บันทึก</button>
        </div>
    </form>

    <h2 class="text-xl font-bold mt-8">รายการวันที่ปิด</h2>
    <table class="min-w-full bg-white shadow rounded-lg mt-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-gray-700">กิจกรรม</th>
                <th class="px-4 py-2 text-left text-gray-700">รอบเวลา</th>
                <th class="px-4 py-2 text-left text-gray-700">วันที่ปิด</th>
            </tr>
        </thead>
        <tbody>
            @foreach($closedDates as $closed)
                <tr>
                    <td class="px-4 py-2">{{ $closed->activity->activity_name }}</td>
                    <td class="px-4 py-2">
                        {{ $closed->timeslot ? $closed->timeslot->start_time . ' - ' . $closed->timeslot->end_time : 'ปิดทุกรอบ' }}
                    </td>
                    <td class="px-4 py-2">{{ $closed->closed_on }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.deleteClosedDate', $closed->closed_timeslots_id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบวันที่ปิดรอบนี้หรือไม่?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">ลบ</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.getElementById('activity_id').addEventListener('change', function () {
        const activityId = this.value;
        const timeslotsDropdown = document.getElementById('timeslots_id');

        if (activityId) {
            fetch("{{ route('admin.getTimeslots') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                body: JSON.stringify({ activity_id: activityId }),
            })
            .then(response => response.json())
            .then(data => {
                timeslotsDropdown.innerHTML = '<option value="">-- เลือกรอบการเข้าชม --</option>';
                timeslotsDropdown.innerHTML += '<option value="all">ปิดทุกรอบ</option>';
                data.forEach(timeslot => {
                    timeslotsDropdown.innerHTML += `<option value="${timeslot.timeslots_id}">${timeslot.start_time} - ${timeslot.end_time}</option>`;
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
@endsection
