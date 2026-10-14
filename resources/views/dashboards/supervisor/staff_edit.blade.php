@extends('layouts.dashboard')
@section('title', 'Edit ' . $user->name)
@section('page-title', 'Edit Staff')
@section('user-role', 'SUPERVISOR')
@section('sidebar-menu') @include('dashboards.supervisor._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Staff Member</h1>
            <p>Update information for {{ $user->name }}</p>
        </div>
        <a href="{{ route('supervisor.staff.show', $user) }}" class="btn-secondary">← Back to Profile</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('supervisor.staff.update', $user) }}">
            @csrf
            @method('PATCH')

            <h3 class="card-title">Personal Information</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="form-group">
                    <label>Hire Date *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $user->hire_date?->format('Y-m-d')) }}" required>
                </div>
            </div>

            <h3 class="card-title" style="margin-top: 25px;">Assignment</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Department *</label>
                    <select name="department" required>
                        @foreach (['Housekeeping', 'Maintenance', 'IT', 'Front Desk'] as $dept)
                            <option value="{{ $dept }}" {{ old('department', $user->department) === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Shift *</label>
                    <select name="shift" required>
                        @foreach (['morning', 'evening', 'night'] as $shift)
                            <option value="{{ $shift }}" {{ old('shift', $user->shift) === $shift ? 'selected' : '' }}>
                                {{ ucfirst($shift) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 10px;">
                <button type="submit" class="btn-primary">💾 Save Changes</button>
                <a href="{{ route('supervisor.staff.show', $user) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
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
        }
        .form-group input:focus,
        .form-group select:focus { border-color: #E84E1B; }
        .error-msg { font-size: 0.75rem; color: #E53935; margin-top: 4px; display: block; }
    </style>
@endsection