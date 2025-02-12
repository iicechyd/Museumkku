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
                                                            <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                            <p class="text-xs text-secondary mb-0">{{ $user->email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        @isset($user->role)
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
                                                        @endisset
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
                                                        @if ($user->role_id !== 1)
                                                            <select name="role_id" class="form-control">
                                                                <option value="" disabled selected>
                                                                    กำหนดสิทธิ์การใช้งาน</option>
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
                                                        @else
                                                            <select name="role_id" class="form-control" disabled>
                                                                <option value="{{ $user->role_id }}" selected>
                                                                    @switch($user->role->role_name ?? null)
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
                                                                            <span class="text-danger">รอกำหนดสิทธิ์</span>
                                                                    @endswitch
                                                                </option>
                                                            </select>
                                                        @endif
                                                    </form>
                                                    </td>
                                                <td>
                                                    @if($user->role->role_name !== 'Super Admin')
                                                        <button type="submit" class="btn btn-success">อนุมัติ</button>
                                                        <form action="{{ route('superadmin.delete_user', $user->user_id) }}" method="POST" style="display:inline;">
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </main>
        </x-layout>
    @endsection
