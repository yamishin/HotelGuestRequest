@extends('layouts.dashboard')
@section('title', 'Profile')
@section('page-title', 'My Profile')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>My Profile</h1>
            <p>Manage your account information</p>
        </div>
    </div>

    <div class="profile-grid">
        <div class="card">
            <h3 class="card-title">Profile Information</h3>
            <div class="profile-avatar">
                <div class="avatar-circle">JD</div>
                <button class="btn-secondary" style="font-size: 0.75rem;">Change Photo</button>
            </div>
            <form class="profile-form">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" value="Jane Doe" />
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="frontdesk@blulane.com" />
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" value="+63 917 000 0000" />
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="Front Desk" disabled />
                </div>
                <button type="submit" class="btn-primary" style="margin-top: 10px;">Save Changes</button>
            </form>
        </div>

        <div class="card">
            <h3 class="card-title">Change Password</h3>
            <form class="profile-form">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" placeholder="••••••••" />
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" placeholder="••••••••" />
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" placeholder="••••••••" />
                </div>
                <button type="submit" class="btn-primary" style="margin-top: 10px;">Update Password</button>
            </form>
        </div>
    </div>

    <style>
        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }
        .profile-avatar { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #f4f4f4; }
        .avatar-circle { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #E84E1B, #ff6b3d); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; }
        .profile-form .form-group { margin-bottom: 18px; }
        .profile-form label { display: block; margin-bottom: 6px; font-size: 0.8rem; font-weight: 600; color: #58595B; }
        .profile-form input { width: 100%; padding: 12px 15px; border: 1px solid #eee; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 0.9rem; outline: none; transition: border 0.3s; }
        .profile-form input:focus { border-color: #E84E1B; }
        .profile-form input:disabled { background: #f9f9f9; color: #999; cursor: not-allowed; }
    </style>
@endsection