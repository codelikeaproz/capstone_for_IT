<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Request Status - MDRRMO Bukidnon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 to-gray-100 min-h-screen">
    <!-- Public Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-10 h-10">
                    <div>
                        <h1 class="font-bold text-xl">MDRRMO Bukidnon</h1>
                        <p class="text-xs text-blue-200">Request Status Portal</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('requests.create') }}" class="btn btn-sm btn-outline text-white border-white hover:bg-white hover:text-blue-900">
                        <i class="fas fa-plus"></i>
                        <span class="hidden sm:inline">New Request</span>
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="hidden sm:inline">Staff Login</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-12 max-w-2xl">
        <!-- Header -->
        <header class="text-center mb-10">
            <div class="inline-block p-4 bg-white rounded-full shadow-lg mb-6">
                <i class="fas fa-search text-5xl text-primary"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                Check Request Status
            </h1>
            <p class="text-base md:text-lg text-gray-600">
                Enter your request number to track the status of your report request
            </p>
        </header>

        <!-- Error Message -->
        @if(isset($error))
            <div class="alert alert-error shadow-lg mb-6" role="alert">
                <div>
                    <i class="fas fa-times-circle text-xl"></i>
                    <div>
                        <h3 class="font-bold">Request Not Found</h3>
                        <p class="text-sm">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search Form -->
        <div class="card bg-white shadow-2xl">
            <div class="card-body p-8">
                <form action="{{ route('requests.status-check') }}" method="GET" class="space-y-6">
                    <div class="form-control">
                        <label for="request_number" class="label">
                            <span class="label-text font-semibold text-gray-700 text-lg">
                                Request Number
                            </span>
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="requestNumber"
                                   id="request_number"
                                   class="input input-bordered input-lg w-full focus:outline-primary pl-12 text-center font-mono font-bold text-xl"
                                   placeholder="REQ-2025-001"
                                   pattern="REQ-\d{4}-\d{3}"
                                   title="Format: REQ-YYYY-XXX (e.g., REQ-2025-001)"
                                   required
                                   aria-required="true"
                                   value="{{ request('requestNumber') }}">
                            <i class="fas fa-hashtag absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                        </div>
                        <label class="label">
                            <span class="label-text-alt text-gray-500">
                                <i class="fas fa-info-circle"></i> Format: REQ-YYYY-XXX (e.g., REQ-2025-001)
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-full gap-3 min-h-[56px]">
                        <i class="fas fa-search text-xl"></i>
                        <span class="text-lg">Check Status</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card bg-white shadow-md hover:shadow-lg transition">
                <div class="card-body">
                    <h3 class="card-title text-base text-primary">
                        <i class="fas fa-clipboard-list"></i>
                        How to Find Your Request Number
                    </h3>
                    <p class="text-sm text-gray-600">
                        Your request number was provided when you submitted your request. Check your email or the confirmation page.
                    </p>
                </div>
            </div>

            <div class="card bg-white shadow-md hover:shadow-lg transition">
                <div class="card-body">
                    <h3 class="card-title text-base text-success">
                        <i class="fas fa-clock"></i>
                        Processing Time
                    </h3>
                    <p class="text-sm text-gray-600">
                        Requests are typically processed within 3-5 business days. You'll be notified of status changes.
                    </p>
                </div>
            </div>

            <div class="card bg-white shadow-md hover:shadow-lg transition">
                <div class="card-body">
                    <h3 class="card-title text-base text-warning">
                        <i class="fas fa-bell"></i>
                        Email Notifications
                    </h3>
                    <p class="text-sm text-gray-600">
                        If you enabled notifications, you'll receive updates via email when your request status changes.
                    </p>
                </div>
            </div>

            <div class="card bg-white shadow-md hover:shadow-lg transition">
                <div class="card-body">
                    <h3 class="card-title text-base text-info">
                        <i class="fas fa-question-circle"></i>
                        Need Help?
                    </h3>
                    <p class="text-sm text-gray-600">
                        For assistance, contact your local MDRRMO office or visit in person with your request number.
                    </p>
                </div>
            </div>
        </div>

        <!-- New Request CTA -->
        <div class="text-center mt-10">
            <p class="text-gray-600 mb-4">Don't have a request yet?</p>
            <a href="{{ route('requests.create') }}" class="btn btn-outline btn-primary btn-lg gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>Submit a New Request</span>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <div class="mb-4">
                <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-12 h-12 mx-auto mb-2">
                <h3 class="font-bold text-lg">Municipal Disaster Risk Reduction Management Office</h3>
                <p class="text-gray-400 text-sm">Province of Bukidnon, Philippines</p>
            </div>
            <div class="flex justify-center gap-6 text-sm">
                <a href="{{ url('/') }}" class="hover:text-blue-400 transition">Home</a>
                <a href="{{ route('requests.create') }}" class="hover:text-blue-400 transition">New Request</a>
                <a href="{{ route('login') }}" class="hover:text-blue-400 transition">Staff Login</a>
            </div>
            <p class="text-gray-500 text-xs mt-4">© {{ date('Y') }} MDRRMO Bukidnon. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>














