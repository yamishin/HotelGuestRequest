@extends('layouts.dashboard')

@section('title', 'Guest Dashboard')
@section('page-title', 'Guest Overview')
@section('user-role', 'GUEST')

@section('sidebar-menu')
    <li><a href="#" class="active">Home</a></li>
    <li><a href="#">Request Service</a></li>
    <li><a href="#">My Request</a></li>
    <li><a href="#">Notification</a></li>
    <li><a href="#">Hotel Info</a></li>
    <li><a href="#">My Stay (Info / Duration)</a></li>
@endsection

@section('content')
    <div class="card">
        <h3>Welcome to your Guest Portal</h3>
        <p style="margin-top:10px; color:#666;">Here you can manage your stay, request room service, and view hotel information.</p>
    </div>
    <div class="card">
        <h3>My Stay Details</h3>
        <p style="margin-top:10px; color:#666;">Room: 402 | Check-in: Oct 12 | Check-out: Oct 15</p>
    </div>
@endsection