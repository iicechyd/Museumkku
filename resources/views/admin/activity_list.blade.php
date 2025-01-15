@extends('layouts.layout_admin')
@section('title', 'ตารางกิจกรรมพิพิธภัณฑ์')
@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('css/activity_list.css') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (count($requestListActivity) > 0)
        <div class="container">
            <h1 class="table-heading text-center">รายละเอียดกิจกรรม</h1>
            <button type="button" class="btn mb-3"
                style="background-color: rgb(249, 100, 100); border-color: rgb(249, 100, 100); color: white;"
                data-toggle="modal" data-target="#InsertActivityModal">
                + กิจกรรม
            </button>
            <button type="button" class="btn mb-3"
                style="background-color: rgb(119, 144, 242); border-color: rgb(119, 144, 242); color: white;"
                onclick="window.location='{{ url('/admin/subactivity_list') }}'">
                หลักสูตร
            </button>
            {{ $requestListActivity->links() }}
            @component('components.table_activity_list')
                @foreach ($requestListActivity as $item)
                    <tr>
                        <td>{{ $item->activity_id }}</td>
                        <td class="long-cell">{{ $item->activity_name }}</td>
                        <td>{{ $item->activityType ? $item->activityType->type_name : 'N/A' }}</td>
                        <td class="long-cell">{{ $item->description }}</td>
                        <td>
                            @if ($item->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                    alt="Image of {{ $item->activity_name }}" width="150" class="image-thumbnail"
                                    data-toggle="modal" data-target="#imageModal" data-activity-id="{{ $item->activity_id }}">
                            @else
                                <p>ไม่มีรูปภาพ</p>
                            @endif
                        </td>
                        <td>
                            @if ($item->max_capacity === null)
                                ไม่จำกัดจำนวนคน
                            @else
                                {{ $item->max_capacity }} คน / รอบ
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-info text-white" data-toggle="modal"
                                data-target="#PricesModal_{{ $item->activity_id }}">
                                แสดงราคา
                            </button>
                        </td>
                        <td>
                            <ul class="list-inline m-0">
                                <li class="list-inline-item">
                                    <button class="btn btn-success btn-sm rounded-0 edit-activity-btn" type="button"
                                        data-toggle="modal" data-target="#EditActivityModal"
                                        data-activity_type_id="{{ $item->activity_type_id }}"
                                        data-id="{{ $item->activity_id }}" data-name="{{ $item->activity_name }}"
                                        data-description="{{ $item->description }}"
                                        data-children_price="{{ $item->children_price }}"
                                        data-student_price="{{ $item->student_price }}"
                                        data-adult_price="{{ $item->adult_price }}"
                                        data-disabled_price="{{ $item->disabled_price }}"
                                        data-elderly_price="{{ $item->elderly_price }}"
                                        data-monk_price="{{ $item->monk_price }}"
                                        data-max_capacity="{{ $item->max_capacity }}"
                                        data-image="{{ asset('storage/' . $item->image) }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </li>
                                <li class="list-inline-item">
                                    <a href="{{ route('delete', $item->activity_id) }}" data-toggle="tooltip"
                                        data-placement="top" title="Delete"
                                        onclick="return confirm('ยืนยันการลบกิจกรรม {{ $item->activity_name }} ?')">
                                        <button class="btn btn-danger btn-sm rounded-0" type="button">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </a>
                                </li>
                            </ul>
                        </td>
                        <td class="text-center" data-name="{{ $item->activity_name }}">
                            <a href="javascript:void(0);" class="toggle-status" data-id="{{ $item->activity_id }}"
                                data-status="{{ $item->status }}">
                                @if ($item->status === 'active')
                                    <i class="fas fa-toggle-on text-success" style="font-size: 24px;" title="Active"></i>
                                @else
                                    <i class="fas fa-toggle-off text-secondary" style="font-size: 24px;" title="Inactive"></i>
                                @endif
                            </a>
                        </td>
                        <!-- Modal สำหรับแสดงราคา -->
                        <div class="modal fade" id="PricesModal_{{ $item->activity_id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ราคา -
                                            {{ $item->activity_name }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>เด็ก
                                            </strong>{{ $item->children_price == 0 ? 'ฟรี' : $item->children_price . ' บาท /คน' }}
                                        </p>
                                        <p><strong>นร/นศ
                                            </strong>{{ $item->student_price == 0 ? 'ฟรี' : $item->student_price . ' บาท /คน' }}
                                        </p>
                                        <p><strong>ผู้ใหญ่
                                            </strong>{{ $item->adult_price == 0 ? 'ฟรี' : $item->adult_price . ' บาท /คน' }}
                                        </p>
                                        <p><strong>ผู้พิการ
                                            </strong>{{ $item->disabled_price == 0 ? 'ฟรี' : $item->disabled_price . ' บาท /คน' }}
                                        </p>
                                        <p><strong>ผู้สูงอายุ
                                            </strong>{{ $item->elderly_price == 0 ? 'ฟรี' : $item->elderly_price . ' บาท /คน' }}
                                        </p>
                                        <p><strong>พระภิกษุสงฆ์ /สามเณร
                                            </strong>{{ $item->monk_price == 0 ? 'ฟรี' : $item->monk_price . ' บาท /รูป' }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </tr>
                @endforeach
            @endcomponent
            </tbody>
            </table>
        </div>
        </div>
    @else
        <h1 class="text text-center py-5 ">ไม่พบข้อมูลในระบบ</h1>
        <button type="button" class="btn my-3"
            style="background-color: rgb(249, 100, 100); border-color: rgb(249, 100, 100); color: white;"
            data-toggle="modal" data-target="#InsertActivityModal">
            + กิจกรรม
        </button>
    @endif

    <!-- Modal สำหรับเพิ่มกิจกรรม -->
    <div class="modal fade" id="InsertActivityModal" tabindex="-1" role="dialog"
        aria-labelledby="InsertActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="InsertActivityModalLabel">เพิ่มกิจกรรมใหม่</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- ฟอร์มเพิ่มกิจกรรม -->
                    <form action="{{ route('insert.activity') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="activity_name">ชื่อกิจกรรม</label>
                            <input type="text" class="form-control" id="activity_name" name="activity_name" required>
                        </div>
                        <div class="form-group">
                            <label for="activity_type_id">ประเภทกิจกรรม</label>
                            <select class="form-control" id="activity_type_id" name="activity_type_id" required>
                                <option value="">เลือกประเภทกิจกรรม</option>
                                @foreach ($activityTypes as $type)
                                    <option value="{{ $type->activity_type_id }}">{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">คำอธิบายกิจกรรม</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="children_price">ราคาเด็ก</label>
                            <input type="number" class="form-control" id="children_price" name="children_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="student_price">ราคานร/นศ</label>
                            <input type="number" class="form-control" id="student_price" name="student_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="adult_price">ราคาผู้ใหญ่</label>
                            <input type="number" class="form-control" id="adult_price" name="adult_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="disabled_price">ราคาผู้พิการ</label>
                            <input type="number" class="form-control" id="disabled_price" name="disabled_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="elderly_price">ราคาผู้สูงอายุ</label>
                            <input type="number" class="form-control" id="elderly_price" name="elderly_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="monk_price">ราคาพระภิกษุสงฆ์ /สามเณร</label>
                            <input type="number" class="form-control" id="monk_price" name="monk_price" min="0"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="max_capacity">ความจุคนต่อรอบการเข้าชม</label>
                            <input type="number" class="form-control" id="max_capacity" name="max_capacity"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="images">เลือกรูปภาพ:</label>
                            <input type="file" name="images[]" multiple>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal สำหรับแก้ไขกิจกรรม -->
    <div class="modal fade" id="EditActivityModal" tabindex="-1" role="dialog"
        aria-labelledby="EditActivityModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditActivityModalLabel">แก้ไขกิจกรรม</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- ฟอร์มแก้ไขกิจกรรม -->
                    <form method="POST" action="/UpdateActivity" enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="hidden" id="edit_activity_id" name="activity_id">
                        <div class="form-group">
                            <label for="edit_activity_type_id">ประเภทกิจกรรม</label>
                            <select class="form-control" id="edit_activity_type_id" name="activity_type_id" required>
                                <option value="">เลือกประเภทกิจกรรม</option>
                                @foreach ($activityTypes as $type)
                                    <option value="{{ $type->activity_type_id }}"
                                        id="edit_type_option_{{ $type->activity_type_id }}">{{ $type->type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_activity_name">ชื่อกิจกรรม</label>
                            <input type="text" class="form-control" id="edit_activity_name" name="activity_name"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">คำอธิบายกิจกรรม</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_childrenprice">ราคาเด็ก</label>
                            <input type="number" class="form-control" id="edit_childrenprice" name="children_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_studentprice">ราคานร/นศ</label>
                            <input type="number" class="form-control" id="edit_studentprice" name="student_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_adultprice">ราคาผู้ใหญ่</label>
                            <input type="number" class="form-control" id="edit_adultprice" name="adult_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_disabledprice">ราคาผู้พิการ</label>
                            <input type="number" class="form-control" id="edit_disabledprice" name="disabled_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_elderlyprice">ราคาผู้สูงอายุ</label>
                            <input type="number" class="form-control" id="edit_elderlyprice" name="elderly_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_monkprice">ราคาพระภิกษุสงฆ์ /สามเณร</label>
                            <input type="number" class="form-control" id="edit_monkprice" name="monk_price"
                                min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_max_capacity">ความจุคนต่อรอบการเข้าชม</label>
                            <input type="number" class="form-control" id="edit_max_capacity" name="max_capacity"
                                min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="images">เลือกรูปภาพ:</label>
                            <input type="file" name="images[]" multiple>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">รูปภาพกิจกรรม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-wrap" id="modal-images">
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/activity_list.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#imageModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var activityId = button.data('activity-id');
    
                $('#modal-images').empty();
    
                $.ajax({
                    url: '/admin/activity/' + activityId + '/images',
                    method: 'GET',
                    success: function(data) {
                        if (data.images.length > 0) {
                            data.images.forEach(function(image) {
                                var imgElement = '<div class="p-2" style="max-width: 300px;"><img src="' + image.url + '" alt="Image" class="img-fluid" style="max-width: 100%; height: auto;"></div>';
                                $('#modal-images').append(imgElement);
                            });
                        } else {
                            $('#modal-images').append('<p>No images available for this activity.</p>');
                        }
                    },
                    error: function() {
                        $('#modal-images').append('<p>Error loading images.</p>');
                    }
                });
            });
        });
    </script>
@endsection
