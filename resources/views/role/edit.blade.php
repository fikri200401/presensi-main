@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('role.update', $role) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions as $resource => $perms)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">{{ ucfirst($resource) }}</h4>
                            <div class="space-y-2">
                                @foreach($perms as $permission)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ str_replace('_', ' ', $permission->name) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('permissions')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('role.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
