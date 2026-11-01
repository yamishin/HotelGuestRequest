@extends('layouts.guest')
@section('title', 'Request Service')

@section('content')
    <div class="card">
        <h2 class="card-title">Request a Service</h2>

        <form method="POST" action="{{ route('guest.request.store') }}">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">What do you need?</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g., Extra towels, Room service dinner..." required
                    style="width: 100%; padding: 14px 16px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; font-size: 0.9rem; outline: none;">
                @error('title') <span style="color: #E53935; font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>


            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">Additional Details (optional)</label>
                <textarea name="description" rows="4" placeholder="Any specific instructions..."
                    style="width: 100%; padding: 14px 16px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; font-size: 0.9rem; outline: none; resize: vertical;">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn-primary">Submit Request</button>
        </form>
    </div>
@endsection