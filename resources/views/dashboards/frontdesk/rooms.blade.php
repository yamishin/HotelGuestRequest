@extends('layouts.dashboard')
@section('title', 'Rooms')
@section('page-title', 'Room Management')
@section('user-role', 'FRONT DESK')
@section('sidebar-menu') @include('dashboards.frontdesk._sidebar') @endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Rooms</h1>
            <p>{{ $roomsByFloor->flatten()->count() }} rooms across floors 5 to 16</p>
        </div>
    </div>

    <div class="card" style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; align-items: center;">
        <div class="room-legend">
            <span><span class="dot available"></span> Available ({{ $roomCounts['available'] }})</span>
            <span><span class="dot occupied"></span> Occupied ({{ $roomCounts['occupied'] }})</span>
            <span><span class="dot cleaning"></span> Cleaning ({{ $roomCounts['cleaning'] }})</span>
            <span><span class="dot maintenance"></span> Maintenance ({{ $roomCounts['maintenance'] }})</span>
        </div>

        <form method="GET" style="display: flex; gap: 10px;">
            <select name="status" onchange="this.form.submit()" style="padding: 10px 15px; border: 1px solid #eee; border-radius: 8px; font-family: 'Montserrat';">
                <option value="all" {{ !request('status') || request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ request('status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="cleaning" {{ request('status') === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </form>
    </div>

    @foreach ($roomsByFloor as $floor => $rooms)
        <div class="card">
            <h3 class="card-title">Floor {{ $floor }}</h3>
            <div class="room-grid">
                @foreach ($rooms as $room)
                    <div class="room {{ $room->status }}" title="Room {{ $room->room_number }} — {{ $room->type }} @if($room->currentStay) · {{ $room->currentStay->guest->full_name }} @endif">
                        {{ $room->room_number }}
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <style>
        .room-legend { display: flex; gap: 20px; font-size: 0.8rem; color: #666; flex-wrap: wrap; align-items: center; }
        .dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 5px; }
        .dot.available { background: #4CAF50; } .dot.occupied { background: #E84E1B; }
        .dot.cleaning { background: #FFC107; } .dot.maintenance { background: #58595B; }
        .room-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 10px; }
        .room { padding: 14px 5px; text-align: center; border-radius: 6px; font-weight: 600; font-size: 0.8rem; color: white; cursor: pointer; transition: transform 0.2s; }
        .room:hover { transform: scale(1.08); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .room.available { background: #4CAF50; } .room.occupied { background: #E84E1B; }
        .room.cleaning { background: #FFC107; color: #58595B; } .room.maintenance { background: #58595B; }
    </style>
@endsection