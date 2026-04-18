@extends('layouts.guest')
@section('title', 'Status Sistem')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg> Kembali
        </a>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-3 mb-6"><div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div><h1 class="text-2xl font-bold text-gray-900">Status Sistem</h1></div>
            <div class="space-y-3">
                @foreach(['HRIS Portal' => 'Operasional', 'Database Server' => 'Operasional', 'Authentication Service' => 'Operasional', 'Notification Service' => 'Operasional', 'GPS / Geolocation' => 'Operasional'] as $service => $status)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <span class="text-sm font-medium text-gray-700">{{ $service }}</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $status }}</span>
                </div>
                @endforeach
            </div>
            <p class="mt-4 text-xs text-gray-400 text-center">Terakhir diperbarui: {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
        </div>
    </div>
</div>
@endsection
