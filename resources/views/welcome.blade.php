<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LaporSana</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-white">
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen selection:bg-red-500 selection:text-white">
        <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            @if (!Auth::check())
                <a href="{{ route('login') }}"
                    class="transition-colors duration-200 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500 px-4 py-2 rounded-lg border border-gray-300 hover:shadow-md">
                    Log in
                </a>
            @else
                <a href="{{ route('logout') }}"
                    class="transition-colors duration-200 font-semibold text-red-600 hover:text-red-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500 px-4 py-2 rounded-lg border border-red-300 hover:shadow-md">
                    Logout
                </a>
            @endif
        </div>
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-12 sm:justify-start sm:pt-0">
                <h1 class="text-5xl font-extrabold text-gray-800 tracking-tight hover:scale-105 transition-transform duration-200">
                    LaporSana
                </h1>
            </div>
            <div class="mt-10 bg-white overflow-hidden shadow-xl border border-gray-200 sm:rounded-2xl">
                <div class="p-8 text-gray-900">
                    <h2 class="text-2xl font-bold mb-2">Welcome to LaporSana!</h2>
                    <p class="mt-4 text-lg leading-relaxed">Laporkan Fasilitas Kampus Yang Rusak, Jangan di Diamkan.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

