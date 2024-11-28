@extends('layouts.layout_super_admin')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">บัญชีที่รออนุมัติ</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="text-right mb-3">
        <a href="{{ route('all_users') }}" class="btn btn-primary">
            <i class="fas fa-clock"></i> รายชื่อบัญชีผู้ใช้งาน
        </a>
    </div>

    @if($users->isEmpty())
        <div class="alert alert-info text-center mt-5">
            ไม่มีบัญชีที่รออนุมัติในขณะนี้
        </div>
    @else
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Assign Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form method="POST" action="{{ route('superadmin.approve_users', $user->user_id) }}">
                                @csrf
                                <select name="role_id" class="form-control">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
