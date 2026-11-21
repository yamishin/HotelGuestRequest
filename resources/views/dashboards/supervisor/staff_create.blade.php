@extends('layouts.dashboard')
@section('title', 'Add Staff')
@section('page-title', 'Add New Staff')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Add New Staff Member</h1>
            <p>Create a new staff account. Login credentials will be generated automatically.</p>
        </div>
        <a href="{{ route('supervisor.staff') }}" class="btn-secondary">← Back to Staff</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('supervisor.staff.store') }}">
            @csrf

            <h3 class="card-title">Personal Information</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+63 917 000 0000">
                    @error('phone') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Hire Date *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', now()->toDateString()) }}" required>
                    @error('hire_date') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <h3 class="card-title" style="margin-top: 25px;">Assignment</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Department *</label>
                    <select name="department" required>
                        <option value="">-- Select a department --</option>
                        <option value="Housekeeping" {{ old('department') === 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                        <option value="Maintenance" {{ old('department') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="IT" {{ old('department') === 'IT' ? 'selected' : '' }}>IT</option>
                        <option value="Front Desk" {{ old('department') === 'Front Desk' ? 'selected' : '' }}>Front Desk</option>
                    </select>
                    @error('department') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Shift *</label>
                    <select name="shift" required>
                        <option value="">-- Select a shift --</option>
                        <option value="morning" {{ old('shift') === 'morning' ? 'selected' : '' }}>Morning (6 AM – 2 PM)</option>
                        <option value="evening" {{ old('shift') === 'evening' ? 'selected' : '' }}>Evening (2 PM – 10 PM)</option>
                        <option value="night" {{ old('shift') === 'night' ? 'selected' : '' }}>Night (10 PM – 6 AM)</option>
                    </select>
                    @error('shift') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="info-box" style="margin-top: 25px;">
                <div class="info-icon">🔑</div>
                <div>
                    <strong>Automatic Credentials</strong>
                    <p>The system will automatically generate an employee code and password. You'll be able to view and copy them after saving.</p>
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 10px;">
                <button type="submit" class="btn-primary">✅ Create Staff Account</button>
                <a href="{{ route('supervisor.staff') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #58595B;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border 0.3s;
            background: white;
        }
        .form-group input:focus,
        .form-group select:focus { border-color: #E84E1B; }
        .error-msg {
            font-size: 0.75rem;
            color: #E53935;
            margin-top: 4px;
            display: block;
        }

        .info-box {
            background: #fff9f6;
            border-left: 4px solid #E84E1B;
            padding: 15px 20px;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        .info-icon { font-size: 1.5rem; }
        .info-box strong { font-size: 0.85rem; color: #58595B; display: block; margin-bottom: 4px; }
        .info-box p { font-size: 0.8rem; color: #888; line-height: 1.5; }
    </style>
@endsection