@extends('Layouts.app')
<head>
    <link rel="stylesheet" href="{{ asset('styles/analytics/analytics.css') }}">
</head>

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container mx-auto px-0 py-2">
    <section class="">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Hello Dashboard</h1>
    </section>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Advanced analytics and reporting will be implemented here.</p>
    </div>

    <section class="">
        <h2 class="pt-10 text-3xl  ">Vehicle Analytics</h2>
    </section>

    <button class="btn btn-neutral">Submit</button>

    <input type="text" placeholder="Type here" class="input input-primary" />


    <div class="flex flex-row gap-2" >
        <button class="btn btn-neutral">Neutral</button>
        <button class="btn btn-primary">Primary</button>
        <button class="btn btn-secondary">Secondary</button>
        <button class="btn btn-accent">Accent</button>
        <button class="btn btn-info">Info</button>
        <button class="btn btn-success">Success</button>
        <button class="btn btn-warning">Warning</button>
        <button class="btn btn-error">Error</button>
    </div>


{{-- Input field with icon and validator --}}

<div class="my-2 mx-2" >
    <label class="input validator input-primary my-0.5">
        <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <g
            stroke-linejoin="round"
            stroke-linecap="round"
            stroke-width="2.5"
            fill="none"
            stroke="currentColor"
          >
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </g>
        </svg>
        <input
          type="text"
          required
          placeholder="Username"
          pattern="[A-Za-z][A-Za-z0-9\-]*"
          minlength="3"
          maxlength="30"
          title="Only letters, numbers or dash"
        />
    </label>
      <p class="validator-hint hidden">Must be 3 to 30 characters
        <br />containing only letters, numbers or dash
      </p>
</div>


<div class="my-2 mx-2" >
    <label class="input validator input-primary my-0.5">
        <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <g
            stroke-linejoin="round"
            stroke-linecap="round"
            stroke-width="2.5"
            fill="none"
            stroke="currentColor"
          >
            <path
              d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"
            ></path>
            <circle cx="16.5" cy="7.5" r=".5" fill="currentColor"></circle>
          </g>
        </svg>
        <input
          type="password"
          required
          placeholder="Password"
          minlength="8"
          pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
          title="Must be more than 8 characters, including number, lowercase letter, uppercase letter"
        />
      </label>
      <p class="validator-hint hidden">
        Must be more than 8 characters, including
        <br />At least one number <br />At least one lowercase letter <br />At least one uppercase letter
      </p>
</div>




<h2 class="text-2xl font-bold text-gray-800 mb-6 border-b border-base-300" >Forms</h2>

@endsection
