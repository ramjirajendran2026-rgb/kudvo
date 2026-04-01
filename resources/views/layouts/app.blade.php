<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kudvo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Ensuring the page takes full height */
        body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; }
        main { flex: 1; }
    </style>
</head>
<body class="bg-white">

   @include('layouts.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

</body>
</html>