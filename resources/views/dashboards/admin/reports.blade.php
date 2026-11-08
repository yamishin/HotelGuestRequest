@extends('layouts.dashboard')
@section('title', 'Global Reports')
@section('page-title', 'Global Reports')
@section('user-role', 'ADMIN')
@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-users-gear"></i> Manage Accounts</a></li>
    <li><a href="{{ route('admin.history') }}" class="{{ request()->routeIs('admin.history') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> Global History</a></li>
    <li><a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Global Reports</a></li>
    <li><a href="{{ route('admin.supervisor.create') }}" class="{{ request()->routeIs('admin.supervisor.create') ? 'active' : '' }}"><i class="fa-solid fa-user-plus"></i> Add Supervisor</a></li>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Global Reports</h1>
            <p>Summary of all service requests across departments</p>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Department Performance Summary</h3>
        
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Total Requests</th>
                        <th>Resolved</th>
                        <th>Unresolved / Rejected</th>
                        <th>Avg. Resolution Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $dept => $data)
                        <tr>
                            <td>
                                <span class="dept-badge dept-{{ Str::slug($dept) }}">
                                    {{ $dept }}
                                </span>
                            </td>
                            <td><strong>{{ $data['total'] }}</strong></td>
                            <td><span class="badge badge-success">{{ $data['resolved'] }}</span></td>
                            <td><span class="badge badge-danger">{{ $data['unresolved'] }}</span></td>
                            <td>{{ $data['avg_time'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .table-wrapper { overflow-x: auto; }
        .dept-badge {
            display: inline-block; padding: 4px 10px; border-radius: 50px;
            font-size: 0.7rem; font-weight: 600; white-space: nowrap;
        }
        .dept-housekeeping { background: #e8f5e9; color: #2e7d32; }
        .dept-maintenance { background: #fff3e0; color: #e65100; }
        .dept-it          { background: #e3f2fd; color: #1565c0; }
        .dept-front-desk  { background: #f3e5f5; color: #6a1b9a; }
    </style>
@endsection