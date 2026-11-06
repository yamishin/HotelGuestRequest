@extends('layouts.guest')
@section('title', 'Request Service')

@section('content')
    <div class="card">
        <h2 class="card-title">Request a Service</h2>

        <form method="POST" action="{{ route('guest.request.store') }}">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">What department do you need?</label>
                <select name="department" id="deptSelect" required onchange="updateCategories()"
                    style="width: 100%; padding: 14px 16px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; font-size: 0.9rem; outline: none; background: white;">
                    <option value="">-- Choose a department --</option>
                    <option value="Housekeeping" {{ old('department') === 'Housekeeping' ? 'selected' : '' }}>Housekeeping (Towels, Cleaning, Pillows)</option>
                    <option value="Maintenance" {{ old('department') === 'Maintenance' ? 'selected' : '' }}>Maintenance (AC, Plumbing, Electrical)</option>
                    <option value="IT" {{ old('department') === 'IT' ? 'selected' : '' }}>IT (Wi-Fi, TV, Phone)</option>
                    <option value="Front Desk" {{ old('department') === 'Front Desk' ? 'selected' : '' }}>Front Desk (Concierge, General Inquiries)</option>
                </select>
                @error('department') <span style="color: #E53935; font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            {{-- DYNAMIC CATEGORY DROPDOWN --}}
            <div style="margin-bottom: 20px; display: none;" id="categoryContainer">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">Specific Issue</label>
                <select name="category" id="categorySelect" onchange="toggleOtherInput()"
                    style="width: 100%; padding: 14px 16px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; font-size: 0.9rem; outline: none; background: white;">
                    <option value="">-- Select the specific issue --</option>
                </select>
            </div>

            {{-- OTHER TEXT INPUT --}}
            <div style="margin-bottom: 20px; display: none;" id="otherInputContainer">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">Please specify your request</label>
                <input type="text" name="other_category" placeholder="Type your specific issue here..."
                    style="width: 100%; padding: 14px 16px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; font-size: 0.9rem; outline: none;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #58595B; margin-bottom: 8px;">Additional Details (optional)</label>
                <textarea name="description" rows="4" placeholder="Any specific instructions..."
                    style="width: 100%; padding: 14px 16px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat'; font-size: 0.9rem; outline: none; resize: vertical;">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn-primary">Submit Request</button>
        </form>
    </div>

    <script>
        const categories = {
            'Housekeeping': ['Extra Towels', 'Extra Pillows', 'Room Cleaning', 'Toiletries / Amenities', 'Other'],
            'Maintenance': ['Air Conditioning Issue', 'Plumbing / Faucet Leak', 'Electrical Issue', 'Broken Furniture', 'Other'],
            'IT': ['Wi-Fi Connection Issue', 'Television Issue', 'Telephone Issue', 'Other'],
            'Front Desk': ['Luggage / Concierge Assistance', 'Late Check-out Request', 'General Inquiry', 'Other']
        };

        function updateCategories() {
            const dept = document.getElementById('deptSelect').value;
            const catContainer = document.getElementById('categoryContainer');
            const catSelect = document.getElementById('categorySelect');
            const otherContainer = document.getElementById('otherInputContainer');

            // Clear existing options
            catSelect.innerHTML = '<option value="">-- Select the specific issue --</option>';
            otherContainer.style.display = 'none';

            if (dept && categories[dept]) {
                catContainer.style.display = 'block';
                categories[dept].forEach(cat => {
                    let option = document.createElement('option');
                    option.value = cat;
                    option.text = cat;
                    catSelect.appendChild(option);
                });
            } else {
                catContainer.style.display = 'none';
            }
        }

        function toggleOtherInput() {
            const val = document.getElementById('categorySelect').value;
            const otherContainer = document.getElementById('otherInputContainer');
            
            if (val === 'Other') {
                otherContainer.style.display = 'block';
            } else {
                otherContainer.style.display = 'none';
            }
        }
    </script>
@endsection