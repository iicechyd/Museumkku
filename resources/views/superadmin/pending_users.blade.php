@extends('layouts.layout_super_admin')

@section('content')
<div class="container mt-5">
    <h2>Pending Users</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Assign Role</th>
                <th>Action</th>
            </tr>
        </thead>
        {{-- <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form method="POST" action="{{ route('superadmin.approve_user', $user->user_id) }}">
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
        </tbody> --}}
    </table>
</div>
@endsection
