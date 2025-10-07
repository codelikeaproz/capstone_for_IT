@extends('Layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New User</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Create user form will be implemented here.</p>
        
        <div class="mt-4">
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Back to Users
            </a>
        </div>
    </div>
</div>
@endsection