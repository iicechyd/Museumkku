@extends('layouts.layout_admin')
@section('title', 'หลักสูตร')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/subactivity_list.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div>
        <h1 class="table-heading text-center">หลักสูตร</h1>
        <button type="button" class="btn my-3" style="background-color: #8BC6EC; border-color: #8BC6EC; color: white;"
            data-toggle="modal" data-target="#InsertSubacitivtyModal">
            เพิ่มหลักสูตร
        </button>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th data-type="text-long">กิจกรรม<span class="resize-handle"></span></th>
                        <th data-type="text-short">หลักสูตร<span class="resize-handle"></span></th>
                        <th data-type="text-short">แก้ไขหลักสูตร<span class="resize-handle"></span></th>
                        <th data-type="text-short">สถานะ<span class="resize-handle"></span></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedActivities = $subActivities->groupBy('activity_id');
                    @endphp
                    @foreach ($groupedActivities as $activityId => $group)
                        @php
                            $rowspan = count($group);
                            $firstRow = true;
                        @endphp
                        @foreach ($group as $index => $item)
                            <tr>
                                @if ($firstRow)
                                    <td rowspan="{{ $rowspan }}">
                                        {{ $item->activity->activity_name }}<br>
                                        จำนวนหลักสูตรที่เลือกได้ {{ $item->activity->max_subactivities }} หลักสูตร
                                        <i class="fas fa-edit" style="color: #3b44ff;" data-id="{{ $item->activity_id }}"
                                            data-max="{{ $item->activity->max_subactivities }}"></i>
                                    </td>
                                    @php $firstRow = false; @endphp
                                @endif

                                <td>{{ $item->sub_activity_name }}</td>
                                <td class="text-center">
                                    <ul class="list-inline m-0">
                                        <li class="list-inline-item">
                                            <button class="btn btn-success btn-sm rounded-0 edit-activity-btn"
                                                type="button" data-id="{{ $item->sub_activity_id }}"
                                                data-name="{{ $item->sub_activity_name }}" data-toggle="modal"
                                                data-target="#EditSubActivityModal">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </li>
                                        <li class="list-inline-item">
                                            <form action="{{ route('admin.deleteSubActivity', $item->sub_activity_id) }}"
                                                method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-0 delete-btn"
                                                    onclick="return confirm('คุณต้องการลบหลักสูตรนี้หรือไม่?');">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="toggle-status"
                                        data-id="{{ $item->sub_activity_id }}" data-name="{{ $item->sub_activity_name }}"
                                        data-status="{{ $item->status }}">
                                        @if ($item->status === 1)
                                            <i class="fas fa-toggle-on text-success" style="font-size: 24px;"
                                                title="Active"></i>
                                        @else
                                            <i class="fas fa-toggle-off text-secondary" style="font-size: 24px;"
                                                title="Inactive"></i>
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal เพิ่มหลักสูตร-->
    <div class="modal fade" id="InsertSubacitivtyModal" tabindex="-1" role="dialog"
        aria-labelledby="InsertSubacitivtyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="InsertSubacitivtyModalLabel">เพิ่มหลักสูตร</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.storeSubActivity') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="activity_id">เลือกกิจกรรม</label>
                            <select name="activity_id" id="activity_id" class="form-control" required>
                                <option value="">เลือกประเภทกิจกรรม</option>
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->activity_id }}">{{ $activity->activity_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sub_activity_name">ชื่อหลักสูตร</label>
                            <input type="text" class="form-control" id="sub_activity_name" name="sub_activity_name"
                                required>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal เพิ่มจำนวนหลักสูตรที่เลือกได้ -->
    <div class="modal fade" id="EditMaxSubactivitiesModal" tabindex="-1" role="dialog"
        aria-labelledby="EditMaxSubactivitiesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditMaxSubactivitiesModalLabel">แก้ไขจำนวนหลักสูตรที่เลือกได้</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editMaxSubactivitiesForm" action="{{ route('admin.updateMaxSubactivities') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="activity_id" id="editActivityId">
                        <div class="form-group">
                            <label for="maxSubactivitiesInput">จำนวนหลักสูตรที่เลือกได้</label>
                            <input type="number" class="form-control" id="maxSubactivitiesInput"
                                name="max_subactivities" min="0" required>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal แก้ไขหลักสูตร-->
    <div class="modal fade" id="EditSubActivityModal" tabindex="-1" role="dialog"
        aria-labelledby="EditSubActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditSubActivityModalLabel">แก้ไขหลักสูตร</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.updateSubActivity', $item->sub_activity_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row text-center justify-content-center">
                            <div class="col-md-8">
                                <input type="text" name="sub_activity_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">บันทึกหลักสูตร</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/subactivity_list.js') }}"></script>
@endsection
