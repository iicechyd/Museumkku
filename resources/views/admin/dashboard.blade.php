@extends('layouts.layout_admin')
@section('title', 'Dashboard')
@section('content')

<title>Dashboard</title>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Users</div>
            <div class="card-body">
                <h5 class="card-title">1,234</h5>
                <p class="card-text">Total registered users.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Posts</div>
            <div class="card-body">
                <h5 class="card-title">567</h5>
                <p class="card-text">Total posts created.</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">Comments</div>
            <div class="card-body">
                <h5 class="card-title">3,890</h5>
                <p class="card-text">Total comments made.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Recent Activity</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">User</th>
                            <th scope="col">Action</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>John Doe</td>
                            <td>Created a new post</td>
                            <td>2024-09-16</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Jane Smith</td>
                            <td>Commented on a post</td>
                            <td>2024-09-15</td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Mike Ross</td>
                            <td>Updated profile</td>
                            <td>2024-09-14</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
