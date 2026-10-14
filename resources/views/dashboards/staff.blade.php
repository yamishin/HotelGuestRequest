@extends('layouts.dashboard')

@section('title', 'Service Staff Dashboard')
@section('page-title', 'My Tasks')
@section('user-role', 'SERVICE STAFF')

@section('sidebar-menu')
    <li><a href="#" class="active">Dashboard</a></li>
    <li><a href="#">Tasks</a></li>
    <li><a href="#">All Department Request</a></li>
    <li><a href="#">Notification</a></li>
    <li><a href="#">History</a></li>
    <li><a href="#">Profile</a></li>
@endsection

@section('content')
    <div class="card">
        <h3>Service Staff Dashboard</h3>
        <p style="margin-top:10px; color:#666;">View your assigned tasks and update their status here.</p>
    </div>
@endsection