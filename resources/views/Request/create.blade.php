<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit Report Request - MDRRMO Bukidnon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .urgency-low { border-color: #10B981; background-color: #D1FAE5; }
        .urgency-medium { border-color: #F59E0B; background-color: #FEF3C7; }
        .urgency-high { border-color: #F97316; background-color: #FFEDD5; }
        .urgency-critical { border-color: #DC2626; background-color: #FEE2E2; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Public Navigation -->
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('img/logo.png') }}" alt="MDRRMO Logo" class="w-10 h-10">
                    <div>
                        <h1 class="font-bold text-xl">MDRRMO Bukidnon</h1>
                        <p class="text-xs text-blue-200">Report Request Portal</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('requests.status-check') }}" class="btn btn-sm btn-outline text-white border-white hover:bg-white hover:text-blue-900">
                        <i class="fas fa-search"></i>
                        <span class="hidden sm:inline">Check Status</span>
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
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Page Header -->
        <header class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                <i class="fas fa-clipboard-list text-primary"></i>
                Submit Report Request
            </h1>
            <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                Request an official incident report from MDRRMO Bukidnon.
                Fill out the form below and our staff will review your request.
            </p>
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-left">
                <h3 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Important Information
                </h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li><i class="fas fa-check text-green-600"></i> Fields marked with <span class="text-error font-semibold">*</span> are required</li>
                    <li><i class="fas fa-check text-green-600"></i> You will receive a request number to track your submission</li>
                    <li><i class="fas fa-check text-green-600"></i> Processing time: 3-5 business days</li>
                    <li><i class="fas fa-check text-green-600"></i> Enable notifications to receive updates</li>
                </ul>
            </div>
        </header>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-error shadow-lg mb-6" role="alert">
                <div>
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <div>
                        <h3 class="font-bold">Please correct the following errors:</h3>
                        <ul class="list-disc list-inside mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Request Form -->
        <form action="{{ route('requests.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6 md:p-8 space-y-8">
            @csrf

            <!-- Section 1: Personal Information -->
            <section aria-labelledby="personal-info-heading">
                <h2 id="personal-info-heading" class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b-2 border-primary flex items-center gap-2">
                    <i class="fas fa-user text-primary"></i>
                    <span>Personal Information</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="form-control md:col-span-2">
                        <label for="requester_name" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Full Name <span class="text-error">*</span>
                            </span>
                        </label>
                        <input type="text"
                               name="requester_name"
                               id="requester_name"
                               class="input input-bordered w-full focus:outline-primary min-h-[44px] @error('requester_name') input-error @enderror"
                               placeholder="Juan dela Cruz"
                               value="{{ old('requester_name') }}"
                               required
                               aria-required="true">
                        @error('requester_name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-control">
                        <label for="requester_email" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Email Address <span class="text-error">*</span>
                            </span>
                        </label>
                        <input type="email"
                               name="requester_email"
                               id="requester_email"
                               class="input input-bordered w-full focus:outline-primary min-h-[44px] @error('requester_email') input-error @enderror"
                               placeholder="juan.delacruz@email.com"
                               value="{{ old('requester_email') }}"
                               required
                               aria-required="true">
                        @error('requester_email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="form-control">
                        <label for="requester_phone" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Phone Number <span class="text-error">*</span>
                            </span>
                        </label>
                        <input type="tel"
                               name="requester_phone"
                               id="requester_phone"
                               class="input input-bordered w-full focus:outline-primary min-h-[44px] @error('requester_phone') input-error @enderror"
                               placeholder="09XX-XXX-XXXX"
                               value="{{ old('requester_phone') }}"
                               required
                               aria-required="true">
                        @error('requester_phone')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- ID Number (Optional) -->
                    <div class="form-control">
                        <label for="requester_id_number" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                ID Number (Optional)
                            </span>
                            <span class="label-text-alt text-gray-500">Driver's License, etc.</span>
                        </label>
                        <input type="text"
                               name="requester_id_number"
                               id="requester_id_number"
                               class="input input-bordered w-full focus:outline-primary min-h-[44px] @error('requester_id_number') input-error @enderror"
                               placeholder="ID Number"
                               value="{{ old('requester_id_number') }}">
                        @error('requester_id_number')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Municipality -->
                    <div class="form-control">
                        <label for="municipality" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Municipality <span class="text-error">*</span>
                            </span>
                        </label>
                        <select name="municipality"
                                id="municipality"
                                class="select select-bordered w-full focus:outline-primary min-h-[44px] @error('municipality') select-error @enderror"
                                required
                                aria-required="true">
                            <option value="">Select Municipality</option>
                            @foreach(array_keys(config('locations.municipalities')) as $municipality)
                                <option value="{{ $municipality }}" {{ old('municipality') == $municipality ? 'selected' : '' }}>
                                    {{ $municipality }}
                                </option>
                            @endforeach
                        </select>
                        @error('municipality')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="form-control md:col-span-2">
                        <label for="requester_address" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Complete Address <span class="text-error">*</span>
                            </span>
                        </label>
                        <textarea name="requester_address"
                                  id="requester_address"
                                  rows="2"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('requester_address') textarea-error @enderror"
                                  placeholder="Street, Barangay, Municipality, Province"
                                  required
                                  aria-required="true">{{ old('requester_address') }}</textarea>
                        @error('requester_address')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Section 2: Request Details -->
            <section aria-labelledby="request-details-heading">
                <h2 id="request-details-heading" class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b-2 border-primary flex items-center gap-2">
                    <i class="fas fa-file-alt text-primary"></i>
                    <span>Request Details</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Request Type -->
                    <div class="form-control md:col-span-2">
                        <label for="request_type" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Type of Report Needed <span class="text-error">*</span>
                            </span>
                        </label>
                        <select name="request_type"
                                id="request_type"
                                class="select select-bordered w-full focus:outline-primary min-h-[44px] @error('request_type') select-error @enderror"
                                required
                                aria-required="true">
                            <option value="">Select Report Type</option>
                            <option value="incident_report" {{ old('request_type') == 'incident_report' ? 'selected' : '' }}>
                                <i class="fas fa-clipboard-list"></i> General Incident Report
                            </option>
                            <option value="traffic_accident_report" {{ old('request_type') == 'traffic_accident_report' ? 'selected' : '' }}>
                                <i class="fas fa-car-crash"></i> Traffic Accident Report
                            </option>
                            <option value="medical_emergency_report" {{ old('request_type') == 'medical_emergency_report' ? 'selected' : '' }}>
                                <i class="fas fa-heartbeat"></i> Medical Emergency Report
                            </option>
                            <option value="fire_incident_report" {{ old('request_type') == 'fire_incident_report' ? 'selected' : '' }}>
                                <i class="fas fa-fire"></i> Fire Incident Report
                            </option>
                            <option value="general_emergency_report" {{ old('request_type') == 'general_emergency_report' ? 'selected' : '' }}>
                                <i class="fas fa-exclamation-triangle"></i> General Emergency Report
                            </option>
                            <option value="vehicle_accident_report" {{ old('request_type') == 'vehicle_accident_report' ? 'selected' : '' }}>
                                <i class="fas fa-ambulance"></i> Vehicle Accident Report
                            </option>
                        </select>
                        @error('request_type')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Urgency Level -->
                    <div class="form-control md:col-span-2">
                        <label class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Urgency Level <span class="text-error">*</span>
                            </span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="urgency_level" value="low"
                                       class="hidden peer"
                                       {{ old('urgency_level') == 'low' ? 'checked' : '' }}
                                       required>
                                <div class="border-2 border-gray-300 peer-checked:urgency-low rounded-lg p-3 text-center transition hover:border-green-500">
                                    <i class="fas fa-info-circle text-green-600 text-2xl mb-1"></i>
                                    <p class="font-semibold text-sm">Low</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="urgency_level" value="medium"
                                       class="hidden peer"
                                       {{ old('urgency_level', 'medium') == 'medium' ? 'checked' : '' }}
                                       required>
                                <div class="border-2 border-gray-300 peer-checked:urgency-medium rounded-lg p-3 text-center transition hover:border-yellow-500">
                                    <i class="fas fa-exclamation-circle text-yellow-600 text-2xl mb-1"></i>
                                    <p class="font-semibold text-sm">Medium</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="urgency_level" value="high"
                                       class="hidden peer"
                                       {{ old('urgency_level') == 'high' ? 'checked' : '' }}
                                       required>
                                <div class="border-2 border-gray-300 peer-checked:urgency-high rounded-lg p-3 text-center transition hover:border-orange-500">
                                    <i class="fas fa-exclamation-triangle text-orange-600 text-2xl mb-1"></i>
                                    <p class="font-semibold text-sm">High</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="urgency_level" value="critical"
                                       class="hidden peer"
                                       {{ old('urgency_level') == 'critical' ? 'checked' : '' }}
                                       required>
                                <div class="border-2 border-gray-300 peer-checked:urgency-critical rounded-lg p-3 text-center transition hover:border-red-500">
                                    <i class="fas fa-skull-crossbones text-red-600 text-2xl mb-1"></i>
                                    <p class="font-semibold text-sm">Critical</p>
                                </div>
                            </label>
                        </div>
                        @error('urgency_level')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Request Description -->
                    <div class="form-control md:col-span-2">
                        <label for="request_description" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Describe Your Request <span class="text-error">*</span>
                            </span>
                            <span class="label-text-alt text-gray-500">Be as detailed as possible</span>
                        </label>
                        <textarea name="request_description"
                                  id="request_description"
                                  rows="4"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('request_description') textarea-error @enderror"
                                  placeholder="Provide details about the incident for which you need a report. Include date, location, and what happened..."
                                  required
                                  aria-required="true">{{ old('request_description') }}</textarea>
                        @error('request_description')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <!-- Purpose of Request -->
                    <div class="form-control md:col-span-2">
                        <label for="purpose_of_request" class="label">
                            <span class="label-text font-semibold text-gray-700">
                                Purpose of Request (Optional)
                            </span>
                            <span class="label-text-alt text-gray-500">Why do you need this report?</span>
                        </label>
                        <textarea name="purpose_of_request"
                                  id="purpose_of_request"
                                  rows="2"
                                  class="textarea textarea-bordered w-full focus:outline-primary @error('purpose_of_request') textarea-error @enderror"
                                  placeholder="e.g., Insurance claim, legal documentation, police report, etc.">{{ old('purpose_of_request') }}</textarea>
                        @error('purpose_of_request')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Section 3: Notification Preferences -->
            <section aria-labelledby="notification-heading">
                <h2 id="notification-heading" class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b-2 border-gray-300 flex items-center gap-2">
                    <i class="fas fa-bell text-gray-600"></i>
                    <span>Notification Preferences</span>
                </h2>
                <p class="text-sm text-gray-600 mb-4">Choose how you want to receive updates about your request</p>

                <div class="space-y-3">
                    <label class="label cursor-pointer justify-start gap-3 bg-base-200 p-4 rounded-lg hover:bg-base-300 transition">
                        <input type="checkbox"
                               name="email_notifications_enabled"
                               value="1"
                               class="checkbox checkbox-primary"
                               {{ old('email_notifications_enabled', true) ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 flex-1">
                            <i class="fas fa-envelope text-primary text-xl"></i>
                            <div>
                                <span class="label-text font-semibold block">Email Notifications</span>
                                <span class="label-text-alt text-gray-600">Receive updates via email</span>
                            </div>
                        </div>
                    </label>

                    <label class="label cursor-pointer justify-start gap-3 bg-base-200 p-4 rounded-lg hover:bg-base-300 transition">
                        <input type="checkbox"
                               name="sms_notifications_enabled"
                               value="1"
                               class="checkbox checkbox-primary"
                               {{ old('sms_notifications_enabled') ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 flex-1">
                            <i class="fas fa-sms text-primary text-xl"></i>
                            <div>
                                <span class="label-text font-semibold block">SMS Notifications</span>
                                <span class="label-text-alt text-gray-600">Receive text message updates (if available)</span>
                            </div>
                        </div>
                    </label>
                </div>
            </section>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t-2 border-gray-200">
                <button type="submit" class="btn btn-primary btn-lg gap-2 flex-1 min-h-[44px]">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Request</span>
                </button>
                <a href="{{ url('/') }}" class="btn btn-outline btn-lg gap-2 sm:w-auto min-h-[44px]">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
            </div>

            <!-- Privacy Notice -->
            <div class="text-xs text-gray-600 text-center mt-4 p-4 bg-gray-100 rounded-lg">
                <i class="fas fa-shield-alt text-primary"></i>
                <strong>Privacy Notice:</strong> Your personal information will be used solely for processing this request and will be handled in accordance with data privacy regulations.
                For questions, contact your local MDRRMO office.
            </div>
        </form>
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
                <a href="{{ route('requests.status-check') }}" class="hover:text-blue-400 transition">Check Status</a>
                <a href="{{ route('login') }}" class="hover:text-blue-400 transition">Staff Login</a>
            </div>
            <p class="text-gray-500 text-xs mt-4">© {{ date('Y') }} MDRRMO Bukidnon. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

