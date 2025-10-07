@extends('Layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">User Management</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">User management functionality will be implemented here.</p>
        
        <div class="mt-4">
            <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add New User
            </a>
        </div>
    </div>
</div>
@endsection