@extends('layouts.guest')
@section('title', 'Review Your Stay')

@section('content')

    @if ($existingReview)
        {{-- ═══════════════════════════════════════════════════════
             ALREADY REVIEWED
             ═══════════════════════════════════════════════════════ --}}
        <div class="card">
            <h3 class="card-title">
                <i class="fa-solid fa-star" style="color: #E84E1B; margin-right: 6px;"></i>
                Your Review
            </h3>

            <div style="display: flex; gap: 4px; font-size: 1.6rem; margin-bottom: 18px;">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star"
                       style="color: {{ $i <= $existingReview->rating ? '#E84E1B' : '#ddd' }};"></i>
                @endfor
                <span style="font-size: 0.9rem; color: #888; margin-left: 10px; align-self: center;">
                    {{ $existingReview->rating }} / 5
                </span>
            </div>

            @if ($existingReview->comment)
                <p style="font-size: 0.9rem; color: #58595B; line-height: 1.7; background: #fafafa; padding: 15px; border-radius: 8px; border-left: 3px solid #E84E1B; margin: 0;">
                    {{ $existingReview->comment }}
                </p>
            @else
                <p style="font-size: 0.85rem; color: #999; font-style: italic;">
                    No comment provided.
                </p>
            @endif

            <p style="font-size: 0.75rem; color: #999; margin-top: 15px;">
                Submitted {{ $existingReview->created_at->format('M d, Y · g:i A') }}
            </p>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('guest.my_stay') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Back to My Stay
            </a>
        </div>

    @elseif ($canReview)
        {{-- ═══════════════════════════════════════════════════════
             ELIGIBLE — SHOW FORM
             ═══════════════════════════════════════════════════════ --}}
        <div class="card">
            <h3 class="card-title">
                <i class="fa-solid fa-star" style="color: #E84E1B; margin-right: 6px;"></i>
                Review Your Stay
            </h3>

            <p style="font-size: 0.9rem; color: #888; margin-bottom: 25px; line-height: 1.6;">
                How was your stay in <strong style="color: #58595B;">Room {{ $stay->room->room_number ?? '—' }}</strong>?
                Your feedback helps us improve.
            </p>

            <form method="POST" action="{{ route('guest.review.store') }}">
                @csrf

                {{-- STAR RATING --}}
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #58595B; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Your Rating
                </label>

                <div id="star-rating" style="display: flex; gap: 10px; font-size: 2.4rem; cursor: pointer; margin-bottom: 8px;">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star"
                           data-value="{{ $i }}"
                           style="color: #ddd; transition: color 0.15s;"
                           onmouseover="hoverStars({{ $i }})"
                           onmouseout="resetStars()"
                           onclick="setRating({{ $i }})"></i>
                    @endfor
                </div>

                <p id="rating-label" style="font-size: 0.85rem; color: #999; margin-bottom: 25px; height: 20px;">
                    Tap a star to rate
                </p>

                <input type="hidden" name="rating" id="rating-value" value="{{ old('rating') }}" required>

                @error('rating')
                    <p style="color: #E53935; font-size: 0.8rem; margin-bottom: 15px;">{{ $message }}</p>
                @enderror

                {{-- COMMENT --}}
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #58595B; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Comment <span style="color: #999; font-weight: 400; text-transform: none; letter-spacing: 0;">(optional)</span>
                </label>

                <textarea name="comment"
                          rows="5"
                          placeholder="Tell us about your stay..."
                          maxlength="1000"
                          style="width: 100%; padding: 12px 15px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-size: 0.9rem; outline: none; resize: vertical; box-sizing: border-box; transition: border-color 0.2s;"
                          onfocus="this.style.borderColor='#E84E1B'"
                          onblur="this.style.borderColor='#eee'">{{ old('comment') }}</textarea>

                @error('comment')
                    <p style="color: #E53935; font-size: 0.8rem; margin-top: 8px;">{{ $message }}</p>
                @enderror

                <div style="display: flex; gap: 10px; margin-top: 25px; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-paper-plane" style="margin-right: 6px;"></i> Submit Review
                    </button>
                    <a href="{{ route('guest.my_stay') }}" class="btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <script>
            let selectedRating = {{ (int) old('rating', 0) }};

            const labels = {
                1: 'Poor',
                2: 'Fair',
                3: 'Good',
                4: 'Very Good',
                5: 'Excellent'
            };

            function hoverStars(n) {
                document.querySelectorAll('#star-rating i').forEach((el, i) => {
                    el.style.color = i < n ? '#E84E1B' : '#ddd';
                });
                document.getElementById('rating-label').textContent = labels[n] || 'Tap a star to rate';
                document.getElementById('rating-label').style.color = n ? '#E84E1B' : '#999';
            }

            function resetStars() {
                document.querySelectorAll('#star-rating i').forEach((el, i) => {
                    el.style.color = i < selectedRating ? '#E84E1B' : '#ddd';
                });
                if (selectedRating > 0) {
                    document.getElementById('rating-label').textContent = labels[selectedRating] || '';
                    document.getElementById('rating-label').style.color = '#E84E1B';
                } else {
                    document.getElementById('rating-label').textContent = 'Tap a star to rate';
                    document.getElementById('rating-label').style.color = '#999';
                }
            }

            function setRating(n) {
                selectedRating = n;
                document.getElementById('rating-value').value = n;
                hoverStars(n);
            }

            // Restore old() value on validation failure
            if (selectedRating > 0) {
                resetStars();
            }
        </script>

    @else
        {{-- ═══════════════════════════════════════════════════════
             FALLBACK — shouldn't normally show, but safe
             ═══════════════════════════════════════════════════════ --}}
        <div class="card" style="text-align: center; padding: 50px 20px;">
            <div style="font-size: 2.5rem; color: #ccc; margin-bottom: 15px;">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h3 style="color: #58595B; margin-bottom: 10px;">Reviews Not Available</h3>
            <p style="color: #888; font-size: 0.9rem; margin-bottom: 25px;">
                You can't submit a review for this stay right now.
            </p>
            <a href="{{ route('guest.my_stay') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i> Back to My Stay
            </a>
        </div>
    @endif

@endsection