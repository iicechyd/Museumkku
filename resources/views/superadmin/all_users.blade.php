@extends('layouts.layout_super_admin')
@section('content')
<div class="container mt-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">บัญชีผู้ใช้งาน</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ประเภทผู้ใช้งาน</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะบัญชีผู้ใช้งาน</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">กำหนดสิทธิ์การใช้งาน</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <img src="{{ asset('assets') }}/img/team-2.jpg"
                                                            class="avatar avatar-sm me-3 border-radius-lg"
                                                            alt="user1">
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $user->email }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->role->role_name ?? 'No Role' }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm bg-gradient-success {{ $user->is_approved ? 'bg-success' : 'bg-warning' }}">{{ $user->is_approved ? 'ใช้งาน' : 'รออนุมัติ' }}</span>
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('superadmin.approve_users', $user->user_id) }}">
                                                    @csrf
                                                    @if($user->role_id !== 1) 
                                                    <select name="role_id" class="form-control">
                                                        <option value="" disabled selected>เลือกสิทธิ์การใช้งาน</option>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @else
                                                    <select name="role_id" class="form-control" disabled>
                                                        <option value="{{ $user->role_id }}" selected>{{ $user->role->role_name }}</option>
                                                    </select>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-success">อนุมัติ</button>
                                                </form>
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
