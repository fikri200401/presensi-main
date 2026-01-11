@extends('layouts.app')

@section('title', 'Edit Leave Request')
@section('page-title', 'Edit Leave Request')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('leave.update', $leave) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select id="user_id" name="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select Employee</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $leave->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $leave->start_date) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $leave->end_date) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                <textarea id="reason" name="reason" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('reason', $leave->reason) }}</textarea>
                @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="pending" {{ old('status', $leave->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ old('status', $leave->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status', $leave->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('leave.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Leave Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
