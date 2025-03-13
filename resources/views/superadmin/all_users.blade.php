@extends('layouts.layout_super_admin')
@section('content')
    <div class="container mt-5">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mt-5">
                {{ session('error') }}
            </div>
        @endif
        <x-layout bodyClass>
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">รายชื่อบัญชีผู้ใช้งาน</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                บัญชีผู้ใช้งาน</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                ประเภทผู้ใช้งาน</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                สถานะบัญชีผู้ใช้งาน</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                กำหนดสิทธิ์การใช้งาน</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <i class="fa-solid fa-user" style="margin-right: 15px;"></i>
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $user->name }}
                                                                <i class="fas fa-edit"
                                                                    style="color: #3b44ff; cursor: pointer;"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editUserModal{{ $user->user_id }}"></i>
                                                            </h6>
                                                            <p class="text-xs text-secondary mb-0">{{ $user->email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        @if ($user->role)
                                                            @switch($user->role->role_name)
                                                                @case('Super Admin')
                                                                    ผู้ดูแลระบบ
                                                                @break

                                                                @case('Admin')
                                                                    เจ้าหน้าที่
                                                                @break

                                                                @case('Executive')
                                                                    ผู้บริหาร
                                                                @break

                                                                @default
                                                                    {{ $user->role->role_name }}
                                                            @endswitch
                                                        @else
                                                            <span style="color: red;">รอกำหนดสิทธิ์</span>
                                                        @endif
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="badge badge-sm bg-gradient-success {{ $user->is_approved ? 'bg-success' : 'bg-warning' }}">{{ $user->is_approved ? 'ใช้งาน' : 'รออนุมัติ' }}</span>
                                                </td>
                                                <td>
                                                    <form method="POST"
                                                        action="{{ route('superadmin.approve_users', $user->user_id) }}">
                                                        @csrf
                                                        <select name="role_id" class="form-control"
                                                            @if ($user->role && $user->role->role_name == 'Super Admin') disabled @endif>
                                                            @if ($user->role && $user->role->role_name == 'Super Admin')
                                                                <option value="" disabled selected>
                                                                    ผู้ดูแลระบบ
                                                                </option>
                                                            @else
                                                                <option value="" disabled selected>
                                                                    กำหนดสิทธิ์การใช้งาน
                                                                </option>
                                                            @endif
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->role_id }}">
                                                                    @switch($role->role_name)
                                                                        @case('Super Admin')
                                                                            ผู้ดูแลระบบ
                                                                        @break

                                                                        @case('Executive')
                                                                            ผู้บริหาร
                                                                        @break

                                                                        @case('Admin')
                                                                            เจ้าหน้าที่
                                                                        @break

                                                                        @default
                                                                            รอกำหนดสิทธิ์
                                                                    @endswitch
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                </td>
                                                <td>
                                                    @if ($user->role && $user->role->role_name !== 'Super Admin')
                                                        <button type="submit" class="btn btn-success">อนุมัติ</button>
                                                    @elseif (!$user->role)
                                                        <button type="submit" class="btn btn-success">อนุมัติ</button>
                                                    @endif
                                                    </form>
                                                    @if ($user->role && $user->role->role_name !== 'Super Admin')
                                                        <form
                                                            action="{{ route('superadmin.delete_user', $user->user_id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger"
                                                                onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีนี้?')">
                                                                ลบ
                                                            </button>
                                                        </form>
                                                    @elseif (!$user->role)
                                                        <form
                                                            action="{{ route('superadmin.delete_user', $user->user_id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger"
                                                                onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีนี้?')">
                                                                ลบ
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                            <!-- Modal แก้ไขชื่อผู้ใช้ -->
                                            <div class="modal fade" id="editUserModal{{ $user->user_id }}" tabindex="-1"
                                                aria-labelledby="editUserModalLabel{{ $user->user_id }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="editUserModalLabel{{ $user->user_id }}">แก้ไขชื่อบัญชีผู้ใช้งาน
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('users.update', $user->user_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="name{{ $user->user_id }}"
                                                                        class="form-label">กรุณากรอกชื่อบัญชีผู้ใช้งาน</label>
                                                                    <input type="text" class="form-control"
                                                                        id="name{{ $user->user_id }}" name="name"
                                                                        value="{{ $user->name }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">ยกเลิก</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">บันทึก</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </main>
        </x-layout>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endsection
