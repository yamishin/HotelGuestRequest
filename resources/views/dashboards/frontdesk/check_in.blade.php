@extends('layouts.dashboard')
@section('title', 'Check-in Guest')
@section('page-title', 'Check-in New Guest')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Check-in New Guest</h1>
            <p>Register a new guest and assign a room</p>
        </div>
        <a href="{{ route('frontdesk.guests_stays') }}" class="btn-secondary">← Back</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('frontdesk.check_in.store') }}">
            @csrf

            <h3 class="card-title">Guest Information</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required>
                    @error('last_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                    @error('email') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <h3 class="card-title" style="margin-top: 25px;">Stay Information</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Room *</label>
                    <select name="room_id" required>
                        <option value="">-- Select a room --</option>
                        @foreach ($availableRooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                Room {{ $room->room_number }} — Floor {{ $room->floor }} ({{ $room->type }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Number of Guests *</label>
                    <input type="number" name="num_guests" value="{{ old('num_guests', 1) }}" min="1" max="10" required>
                </div>
                <div class="form-group">
                    <label>Check-in Date *</label>
                    <input type="date" name="check_in_date" value="{{ old('check_in_date', now()->toDateString()) }}" required>
                    @error('check_in_date') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Check-out Date *</label>
                    <input type="date" name="check_out_date" value="{{ old('check_out_date', now()->addDays(1)->toDateString()) }}" required>
                    @error('check_out_date') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label>Special Requests / Notes</label>
                <textarea name="special_requests" rows="3" placeholder="Any preferences, allergies, or requests...">{{ old('special_requests') }}</textarea>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 10px;">
                <button type="submit" class="btn-primary">✅ Confirm Check-in</button>
                <a href="{{ route('frontdesk.guests_stays') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.8rem; font-weight: 600; color: #58595B; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 12px 15px; border: 1px solid #eee; border-radius: 8px;
            font-family: 'Montserrat', sans-serif; font-size: 0.9rem; outline: none;
            transition: border 0.3s; background: white;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #E84E1B; }
        .form-group textarea { resize: vertical; }
        .error-msg { font-size: 0.75rem; color: #E53935; margin-top: 4px; display: block; }
    </style>
@endsection