@extends('layouts.dashboard')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('user-role', 'STAFF')
@section('sidebar-menu') @include('dashboards.staff._sidebar') @endsection

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
                <div class="avatar-circle">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #58595B;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 0.75rem; color: #999;">{{ auth()->user()->employee_code }} · {{ auth()->user()->department }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('staff.profile.update') }}" class="profile-form">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" disabled>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                    @error('phone') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Department</label>
                    <input type="text" value="{{ auth()->user()->department }}" disabled>
                </div>

                <div class="form-group">
                    <label>Shift</label>
                    <input type="text" value="{{ ucfirst(auth()->user()->shift) }}" disabled>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 10px;">💾 Save Changes</button>
            </form>
        </div>

        <div class="card">
            <h3 class="card-title">Change Password</h3>

            <form method="POST" action="{{ route('staff.profile.password') }}" class="profile-form">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="••••••••" required>
                    @error('current_password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="At least 6 characters" required>
                    @error('new_password') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" placeholder="Repeat new password" required>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 10px;">🔑 Update Password</button>
            </form>
        </div>
    </div>

    <style>
        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }
        .profile-avatar {
            display: flex; align-items: center; gap: 15px;
            margin-bottom: 25px; padding-bottom: 20px;
            border-bottom: 1px solid #f4f4f4;
        }
        .avatar-circle {
            width: 60px; height: 60px; border-radius: 50%;
            background: linear-gradient(135deg, #E84E1B, #ff6b3d);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; font-weight: 700;
        }
        .profile-form .form-group { margin-bottom: 18px; }
        .profile-form label {
            display: block; margin-bottom: 6px;
            font-size: 0.8rem; font-weight: 600; color: #58595B;
        }
        .profile-form input {
            width: 100%; padding: 12px 15px;
            border: 1px solid #eee; border-radius: 8px;
            font-family: 'Montserrat', sans-serif; font-size: 0.9rem;
            outline: none;
        }
        .profile-form input:focus { border-color: #E84E1B; }
        .profile-form input:disabled { background: #f9f9f9; color: #999; cursor: not-allowed; }
        .error-msg { font-size: 0.75rem; color: #E53935; margin-top: 4px; display: block; }
    </style>
@endsection