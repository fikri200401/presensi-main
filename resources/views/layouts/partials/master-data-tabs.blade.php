<!-- Breadcrumb -->
<div class="mb-4">
    <nav class="flex items-center gap-1.5 text-sm text-gray-400">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-600 transition-colors">Admin Portal</a>
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
        <span class="text-gray-600 font-medium">Master Data</span>
    </nav>
</div>

<!-- Page Title -->
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Master Data Management</h2>
    <p class="text-sm text-gray-500 mt-0.5">Kelola semua data master sistem HRIS</p>
</div>

<!-- Tab Navigation -->
<div class="mb-6 border-b border-gray-200">
    <nav class="flex gap-0 -mb-px overflow-x-auto">
        @can('view_any_user')
        <a href="{{ route('user.index') }}" class="{{ request()->routeIs('user.*') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">
            Karyawan
        </a>
        @endcan
        @can('view_any_office')
        <a href="{{ route('office.index') }}" class="{{ request()->routeIs('office.*') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">
            Lokasi
        </a>
        @endcan
        @can('view_any_shift')
        <a href="{{ route('shift.index') }}" class="{{ request()->routeIs('shift.*') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">
            Jam Kerja
        </a>
        @endcan
        @can('view_any_schedule')
        <a href="{{ route('schedule.index') }}" class="{{ request()->routeIs('schedule.*') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">
            Jadwal
        </a>
        @endcan
        @can('view_any_role')
        <a href="{{ route('role.index') }}" class="{{ request()->routeIs('role.*') ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">
            Jabatan
        </a>
        @endcan
    </nav>
</div>
