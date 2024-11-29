@extends('layouts.layout_admin')
@section('title', 'แก้ไขรอบการเข้าชม')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/timeslots_list.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div>
        <h1 class="table-heading text-center">รอบการเข้าชม</h1>
        <button type="button" class="btn my-3"
            style="background-color: rgb(85, 88, 218); border-color: rgb(85, 88, 218); color: white;" data-toggle="modal"
            data-target="#InsertTimeslotsModal">
            เพิ่มรอบการเข้าชม
        </button>

        <div class="table-responsive">
            @if ($activities->isEmpty() || $activities->every(fn($activity) => $activity->timeslots->isEmpty()))
                <div class="text-center mt-4">
                    <h1 class="text text-center py-5 ">ไม่พบข้อมูลในระบบ</h1>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th data-type="text-long">ชื่อกิจกรรม<span class="resize-handle"></span></th>
                            <th data-type="text-short">รอบที่<span class="resize-handle"></span></th>
                            <th data-type="text-short">รอบการเข้าชม<span class="resize-handle"></span></th>
                            <th data-type="text-short">แก้ไขเวลา<span class="resize-handle"></span></th>
                            <th data-type="text-short">สถานะ<span class="resize-handle"></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            @php
                                $rowspan = $activity->timeslots->count();
                            @endphp
                            @foreach ($activity->timeslots as $index => $timeslot)
                                <tr>
                                    @if ($index == 0)
                                        <td rowspan="{{ $rowspan }}">{{ $activity->activity_name }}</td>
                                    @endif
                                    <td>{{ $index + 1 }}</td>

                                    <td>{{ \Carbon\Carbon::parse($timeslot->start_time)->format('H:i') }} น. -
                                        {{ \Carbon\Carbon::parse($timeslot->end_time)->format('H:i') }} น.</td>
                                    <td>
                                        <ul class="list-inline m-0">
                                            <li class="list-inline-item">
                                                <button class="btn btn-success btn-sm rounded-0" type="button"
                                                    data-toggle="modal"
                                                    data-target="#EditTimeslotModal{{ $timeslot->timeslots_id }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </li>
                                            {{-- <li class="list-inline-item">
                                                <form action="{{ route('timeslots.destroy', $timeslot->timeslots_id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('คุณต้องการลบรอบการเข้าชมหรือไม่?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm rounded-0" type="submit">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </li> --}}
                                        </ul>
                                    </td>
                                    <td class="text-center"> 
                                        <a href="javascript:void(0);" 
                                           class="toggle-status" 
                                           data-id="{{ $timeslot->timeslots_id }}" 
                                           data-status="{{ $timeslot->status }}">
                                            @if($timeslot->status === 'active')
                                                <i class="fas fa-toggle-on text-success" style="font-size: 24px;" title="Active"></i>
                                            @else
                                                <i class="fas fa-toggle-off text-secondary" style="font-size: 24px;" title="Inactive"></i>
                                            @endif
                                        </a>
                                    </td>
                                    
                                </tr>
                                <!-- Modal สำหรับการแก้ไขรอบการเข้าชม -->
                                <div class="modal fade" id="EditTimeslotModal{{ $timeslot->timeslots_id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="EditTimeslotModalLabel{{ $timeslot->timeslots_id }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="EditTimeslotModalLabel{{ $timeslot->timeslots_id }}">
                                                    แก้ไขรอบการเข้าชม
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('timeslots.update', $timeslot->timeslots_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="start_time">เวลาเริ่มต้น:</label>
                                                        <input type="time" name="start_time" id="start_time"
                                                            class="form-control"
                                                            value="{{ \Carbon\Carbon::parse($timeslot->start_time)->format('H:i') }}"
                                                            required>

                                                    </div>
                                                    <div class="form-group">
                                                        <label for="end_time">เวลาสิ้นสุด:</label>
                                                        <input type="time" name="end_time" id="end_time"
                                                            class="form-control"
                                                            value="{{ \Carbon\Carbon::parse($timeslot->end_time)->format('H:i') }}"
                                                            required>
                                                    </div>
                                                    <div class="pt-2">
                                                        <button type="submit"
                                                            class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
            <!-- Modal สำหรับเพิ่มรอบกิจกรรม -->
            <div class="modal fade" id="InsertTimeslotsModal" tabindex="-1" role="dialog"
                aria-labelledby="InsertTimeslotsModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="InsertTimeslotsModalLabel">เพิ่มรอบการเข้าชมของกิจกรรม</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- ฟอร์มเพิ่มรอบการเข้าชม -->
                            <form action="{{ route('InsertTimeslots') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="activity_id">กิจกรรม:</label>
                                    <select name="activity_id" id="activity_id" class="form-control">
                                        @foreach ($activities as $activity)
                                            <option value="{{ $activity->activity_id }}">
                                                {{ $activity->activity_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="start_time">เวลาเริ่มต้น:</label>
                                    <input type="time" name="start_time" id="start_time" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_time">เวลาสิ้นสุด:</label>
                                    <input type="time" name="end_time" id="end_time" class="form-control" required>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="max_capacity">ความจุสูงสุดต่อรอบ</label>
                                    <input type="number" name="max_capacity" id="max_capacity" class="form-control"
                                        required>
                                </div> --}}
                                <div class="pt-3">
                                    <button type="submit" class="btn btn-primary ">เพิ่มรอบการเข้าชม</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/timeslots_list.js') }}"></script>
@endsection
