@extends('layouts.dashboard')
@section('title', 'Add Supervisor')
@section('page-title', 'Add New Supervisor')
@section('user-role', 'ADMIN')
@section('sidebar-menu')
    <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-users-gear"></i> Manage Accounts</a></li>
    <li><a href="{{ route('admin.supervisor.create') }}" class="active"><i class="fa-solid fa-user-plus"></i> Add Supervisor</a></li>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Add New Supervisor</h1>
            <p>Create a supervisor account for a specific department</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.supervisor.store') }}">
            @csrf
            
            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label>Assign to Department *</label>
                    <select name="department" required>
                        <option value="">-- Select Department --</option>
                        <option value="Front Desk" {{ old('department') == 'Front Desk' ? 'selected' : '' }}>Front Desk</option>
                        <option value="Housekeeping" {{ old('department') == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                        <option value="Maintenance" {{ old('department') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="IT" {{ old('department') == 'IT' ? 'selected' : '' }}>IT</option>
                    </select>
                    @error('department') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-top: 25px;">
                <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Create Supervisor Account</button>
            </div>
        </form>
    </div>
@endsection