@extends('layouts.guest')
@section('title', 'Leave a Review')

@section('content')
    <div class="card">
        <h2 class="card-title">Leave a Review</h2>
        <p style="font-size: 0.9rem; color: #666; margin-bottom: 25px;">
            We'd love to hear about your stay, {{ $currentStay->guest->first_name }}!
        </p>

        <form method="POST" action="{{ route('guest.review.store') }}">
            @csrf

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 12px;">Your Rating</label>
                <div class="rating-input">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                        <label for="star{{ $i }}">★</label>
                    @endfor
                </div>
                @error('rating') <span style="color: #E53935; font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">Your Comments (optional)</label>
                <textarea name="comment" rows="5" placeholder="Tell us about your experience..."
                    style="width: 100%; padding: 14px 16px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; font-size: 0.9rem; outline: none; resize: vertical;">{{ old('comment') }}</textarea>
            </div>

            <button type="submit" class="btn-primary">Submit Review</button>
        </form>
    </div>

    <style>
        .rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; }
        .rating-input input { display: none; }
        .rating-input label {
            font-size: 2.5rem; color: #ddd; cursor: pointer; transition: color 0.2s;
        }
        .rating-input input:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label { color: #E84E1B; }
    </style>
@endsection