@extends('layouts.dashboard')

@section('title', 'Supervisor Dashboard')
@section('page-title', 'Supervisor Control Panel')
@section('user-role', 'SUPERVISOR')

@section('sidebar-menu')
    <li><a href="#" class="active">Dashboard</a></li>
    <li><a href="#">Request Queue</a></li>
    <li><a href="#">Staff</a></li>
    <li><a href="#">Assignments</a></li>
    <li><a href="#">History</a></li>
    <li><a href="#">Notification</a></li>
@endsection

@section('content')
    <div class="card">
        <h3>Supervisor Dashboard</h3>
        <p style="margin-top:10px; color:#666;">Oversee requests, assign tasks to staff, and manage departments.</p>
    </div>
@endsection