<!DOCTYPE html>
@extends('Layouts.app')

@section('title', 'Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
@endpush

@section('content')
<!-- Dashboard Header -->
<div class="bg-white shadow-sm py-4 px-6 flex items-center justify-between mb-6">
    <div class="flex items-center">
        <h1 class="text-2xl font-bold text-gray-800">Staff Dashboard</h1>
    </div>
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('incidents.create') }}" class="btn btn-error">
                <i class="fas fa-plus mr-2"></i>Report Incident
            </a>
        </div>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <i class="fas fa-bell text-gray-500 text-xl cursor-pointer"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </div>
            <div class="dropdown relative">
                <div class="flex items-center cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-user text-brick-orange"></i>
                    </div>
                    <span class="ml-4 text-gray-700">{{ auth()->user()->first_name ?? 'Staff' }}</span>
                    <i class="fas fa-chevron-down ml-1 text-gray-500 text-xs"></i>
                </div>
                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <div class="border-t border-gray-200"></div>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="p-6 bg-gray-50" data-theme="corporate">
                @yield('content')
                <!-- You can open the modal using ID.showModal() method -->
                <button class="btn btn-accent" onclick="my_modal_4.showModal()">open modal</button>
                <dialog id="my_modal_4" class="modal">
                    <div class="modal-box w-11/12 max-w-5xl">
                        <h3 class="text-lg font-bold">Hello!</h3>
                        <p class="py-4">Click the button below to close</p>

                        <input type="text" placeholder="Type here" class="input" />
                        <input type="text" placeholder="Type here" class="input" />
                        <input type="text" placeholder="Type here" class="input" />
                        <input type="text" placeholder="Type here" class="input" />
                        <input type="text" placeholder="Type here" class="input" />
                        <div class="modal-action">
                            <form method="dialog">
                                <!-- if there is a button, it will close the modal -->
                                <button class="btn btn-accent  ">Save</button>
                                <button class="btn btn-secondary ">Close</button>


                            </form>
                        </div>
                    </div>
                </dialog>
                <button class="btn btn-neutral">Neutral</button>
                <button class="btn btn-primary">Primary</button>
                <button class="btn btn-outline">Secondary</button>
                <button class="btn btn-accent">Accent</button>
                <button class="btn btn-info">Info</button>
                <button class="btn btn-success">Success</button>
                <button class="btn btn-warning">Warning</button>
                <button class="btn btn-error">Error</button>

</div>
@endsection

@push('scripts')
<script>
// Dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        const dropdownToggle = dropdown.querySelector('.dropdown');

        if (dropdownToggle && dropdownMenu) {
            dropdownToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('hidden');
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
});
</script>
@endpush
