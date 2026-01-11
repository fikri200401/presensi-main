@extends('layouts.app')

@section('title', 'Edit Attendance')
@section('page-title', 'Edit Attendance')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('attendance.update', $attendance) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select id="user_id" name="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select Employee</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $attendance->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="schedule_id" class="block text-sm font-medium text-gray-700">Schedule</label>
                <select id="schedule_id" name="schedule_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select Schedule</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}" {{ old('schedule_id', $attendance->schedule_id) == $schedule->id ? 'selected' : '' }}>
                            {{ $schedule->shift->name ?? 'N/A' }} - {{ $schedule->office->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('schedule_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="schedule_latitude" class="block text-sm font-medium text-gray-700">Schedule Latitude</label>
                    <input type="number" step="any" id="schedule_latitude" name="schedule_latitude" value="{{ old('schedule_latitude', $attendance->schedule_latitude) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('schedule_latitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="schedule_longitude" class="block text-sm font-medium text-gray-700">Schedule Longitude</label>
                    <input type="number" step="any" id="schedule_longitude" name="schedule_longitude" value="{{ old('schedule_longitude', $attendance->schedule_longitude) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('schedule_longitude')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="schedule_start_time" class="block text-sm font-medium text-gray-700">Schedule Start Time</label>
                    <input type="time" id="schedule_start_time" name="schedule_start_time" value="{{ old('schedule_start_time', $attendance->schedule_start_time) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('schedule_start_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="schedule_end_time" class="block text-sm font-medium text-gray-700">Schedule End Time</label>
                    <input type="time" id="schedule_end_time" name="schedule_end_time" value="{{ old('schedule_end_time', $attendance->schedule_end_time) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('schedule_end_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('attendance.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Attendance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
