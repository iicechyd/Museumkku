@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Pending User Approvals</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Assign Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form action="{{ route('superadmin.approve_user', $user->user_id) }}" method="POST">
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
</div>
@endsection
