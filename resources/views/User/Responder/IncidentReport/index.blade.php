@extend('Layout.app')

<main>
    <section class="block">
        <a href="{{ route('dashboard') }}"></a>
        <h1>Hello {{ $user->first_name }}</h1>

        <button class="btn btn-circle" type="button">Submit</button>



        <ul>
            @foreach ($user as $user )

            @endforeach
        </ul>


    </section>
</main>
