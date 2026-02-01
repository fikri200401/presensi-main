@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance Management')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <div class="flex-1 max-w-lg">
        <form method="GET" action="{{ route('attendance.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by employee name..." 
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <input type="date" name="date" value="{{ request('date') }}" 
                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Search
            </button>
        </form>
    </div>
    @can('create_attendance')
        @if(auth()->user()->hasRole(['super_admin', 'admin']))
            <a href="{{ route('attendance.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add New
            </a>
        @endif
    @endcan
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attendances as $attendance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                {{ substr($attendance->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $attendance->user->schedule->shift->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $attendance->schedule_start_time }} - {{ $attendance->schedule_end_time }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }} - 
                        {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $attendance->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-2">
                            @can('update_attendance')
                                @if(auth()->user()->hasRole(['super_admin', 'admin']) || $attendance->user_id === auth()->id())
                                    <a href="{{ route('attendance.edit', $attendance) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                            @endcan
                            
                            @can('delete_attendance')
                                @if(auth()->user()->hasRole(['super_admin', 'admin']))
                                    <form method="POST" action="{{ route('attendance.destroy', $attendance) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                @endif
                            @endcan
                            
                            @if(!auth()->user()->can('update_attendance') && !auth()->user()->can('delete_attendance'))
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No attendance records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
