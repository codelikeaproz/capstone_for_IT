<!DOCTYPE html>
<html lang="en" data-theme="corporate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @if(session('success'))
        <div class="toast toast-top toast-end">
            <div class="alert alert-success">
                <span class="text-white">Message Success.</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="toast toast-top toast-end">
            <div class="alert alert-error">
                <span class="text-white">Message Error.</span>
            </div>
        </div>
    @endif


    @if(session('warning'))
        <div class="toast toast-top toast-end">
            <div class="alert alert-warning">
                <span class="text-white">Message Warning.</span>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="toast toast-top toast-end">
            <div class="alert alert-info">
                <span class="text-white">Message Info.</span>
            </div>
        </div>
    @endif

</body>
</html>
