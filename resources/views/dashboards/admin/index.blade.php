@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('page-title', 'Management Dashboard')
@section('user-role', 'ADMIN')

@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="fa-solid fa-users-gear"></i> Manage Accounts</a></li>
    <li><a href="{{ route('admin.supervisor.create') }}"><i class="fa-solid fa-user-plus"></i> Add Supervisor</a></li>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Management Dashboard</h1>
            <p>Oversee Supervisors and Staff accounts across all departments</p>
        </div>
        <a href="{{ route('admin.supervisor.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Add Supervisor</a>
    </div>

    <div class="card">
        <h3 class="card-title">Department Supervisors</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supervisors as $sup)
                    <tr>
                        <td><strong>{{ $sup->name }}</strong></td>
                        <td>{{ $sup->email }}</td>
                        <td><span class="badge badge-grey">{{ $sup->department }}</span></td>
                        <td><span class="badge badge-{{ $sup->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($sup->status) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.user.toggleStatus', $sup) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-secondary btn-sm">{{ $sup->status === 'active' ? 'Deactivate' : 'Activate' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 class="card-title">All Staff Members</h3>
        <table class="data-table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Department</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($staff as $st)
                    <tr>
                        <td>{{ $st->name }}</td>
                        <td>{{ $st->email }}</td>
                        <td>{{ $st->department }}</td>
                        <td><span class="badge badge-{{ $st->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($st->status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection