@extends('layouts.app')

@section('title', 'Edit Schedule')
@section('page-title', 'Edit Schedule')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('schedule.update', $schedule) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select id="user_id" name="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select Employee</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $schedule->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="shift_id" class="block text-sm font-medium text-gray-700">Shift</label>
                <select id="shift_id" name="shift_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select Shift</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ old('shift_id', $schedule->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                    @endforeach
                </select>
                @error('shift_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="office_id" class="block text-sm font-medium text-gray-700">Office</label>
                <select id="office_id" name="office_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id', $schedule->office_id) == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                    @endforeach
                </select>
                @error('office_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center">
                    <input type="checkbox" id="is_wfa" name="is_wfa" value="1" {{ old('is_wfa', $schedule->is_wfa) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_wfa" class="ml-2 block text-sm text-gray-900">Work From Anywhere (WFA)</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_banned" name="is_banned" value="1" {{ old('is_banned', $schedule->is_banned) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_banned" class="ml-2 block text-sm text-gray-900">Banned</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('schedule.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
