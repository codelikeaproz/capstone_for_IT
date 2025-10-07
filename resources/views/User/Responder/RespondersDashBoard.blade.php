<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mobile Responders Dashboard</title>
</head>
<body>
    @include('Components.Navbar')

    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Mobile Responders Dashboard</h1>
        <p>Welcome to the Mobile Responders Dashboard. Here you can manage your tasks and view important information.</p>

        <!-- Add your dashboard content here -->
        {{-- only reporting type of accident , vehicle vs vehicle , and its type --}}
        {{-- report including img or video optional --}}
        {{-- this is the responders role and task can do --}}
        {{--
            to be reported:
            - type of accident
            - vehicle vs vehicle
            - vehicle vs pedestrian
            - vehicle vs property
            - vehicle vs animal
            - vehicle vs cyclist
            - vehicle vs other
            - vehicle vs unknown
            - vehicle vs other vehicle

            --for each type emergency
            - maternity different modal appears
            - medical different modal appears
            

            --once it reported it cannot be updated and deleted
            -- it can add people involved in the accident
            -- it can add vehicle involved in the accident
            -- it can add property involved in the accident

            -- it can add animal involved in the accident
            -- it can add cyclist involved in the accident
            -- it can add pedestrian involved in the accident

            --it can updated but in specific part like for the victim if its reffered to other hospital


            -type of vehicle
            - type of pedestrian
            - type of property
            - type of animal
            - type of cyclist
            - type of vehicle license plate
            - type of vehicle color
            - type of vehicle model

            location of accident
            - latitude
            - longitude
            - address

            - time of accident
            - date of accident

            - weather condition
            - road condition
            - visibility condition
            - traffic condition

        --}}
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-2">Tasks</h2>
            <ul class="list-disc pl-5">
                <li>Task 1: Respond to emergency calls</li>
                <li>Task 2: Update incident reports</li>
                <li>Task 3: Coordinate with other responders</li>
                <li>Task 4: Review training materials</li>
                <li>Task 5: Attend scheduled briefings</li>

            </ul>
        </div>
    </div>

    @include('Components.Footer')
    

</body>
</html>
