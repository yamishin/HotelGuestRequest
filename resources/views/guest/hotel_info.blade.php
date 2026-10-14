@extends('layouts.guest')
@section('title', 'Hotel Information')

@section('content')
    <div class="card">
        <h2 class="card-title">Hotel Information</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
            <div>
                <h3 style="font-size: 0.9rem; color: #E84E1B; margin-bottom: 10px;">🏨 Front Desk</h3>
                <p style="font-size: 0.85rem; color: #666; line-height: 1.7;">
                    Available 24/7<br>
                    Dial <strong>0</strong> from your room<br>
                    Location: Lobby, Ground Floor
                </p>
            </div>
            <div>
                <h3 style="font-size: 0.9rem; color: #E84E1B; margin-bottom: 10px;">🍽️ Restaurant</h3>
                <p style="font-size: 0.85rem; color: #666; line-height: 1.7;">
                    Breakfast: 6:00 AM – 10:00 AM<br>
                    Lunch: 11:30 AM – 2:30 PM<br>
                    Dinner: 6:00 PM – 10:00 PM<br>
                    Location: 5th Floor
                </p>
            </div>
            <div>
                <h3 style="font-size: 0.9rem; color: #E84E1B; margin-bottom: 10px;">🏊 Pool & Gym</h3>
                <p style="font-size: 0.85rem; color: #666; line-height: 1.7;">
                    Pool: 6:00 AM – 10:00 PM<br>
                    Gym: 24 hours (keycard access)<br>
                    Location: 6th Floor
                </p>
            </div>
            <div>
                <h3 style="font-size: 0.9rem; color: #E84E1B; margin-bottom: 10px;">💆 Spa</h3>
                <p style="font-size: 0.85rem; color: #666; line-height: 1.7;">
                    Open: 10:00 AM – 9:00 PM<br>
                    Book at the front desk<br>
                    Location: 7th Floor
                </p>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">House Rules</h2>
        <ul style="padding-left: 20px; font-size: 0.85rem; color: #666; line-height: 2;">
            <li>Check-out time is 12:00 NN. Late check-out may incur charges.</li>
            <li>Please keep noise levels down after 10:00 PM.</li>
            <li>Smoking is strictly prohibited inside the rooms.</li>
            <li>Pets are not allowed unless pre-approved.</li>
            <li>For any emergency, dial <strong>911</strong> or <strong>0</strong> for the front desk.</li>
        </ul>
    </div>

    <div class="card">
        <h2 class="card-title">Wi-Fi Information</h2>
        <p style="font-size: 0.9rem; color: #666; line-height: 1.7;">
            Network: <strong>Blulane_Guest</strong><br>
            Password: <strong>welcome2026</strong>
        </p>
    </div>
@endsection