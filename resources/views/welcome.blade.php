<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LaporSana</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(45deg); }
            50% { transform: translateY(-12px) rotate(45deg); }
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 0.3; box-shadow: 0 0 8px #facc15; }
            50% { opacity: 0.6; box-shadow: 0 0 16px #facc15; }
        }

        .glow-button {
            transition: all 0.3s ease-in-out;
        }

        .glow-button:hover {
            box-shadow: 0 0 25px rgba(59, 130, 246, 0.5);
            transform: scale(1.05);
        }
    </style>
</head>

<body class="bg-gray-900 text-white overflow-x-hidden">
    <!-- Hero Section -->
    <div class="relative min-h-screen bg-cover bg-center bg-no-repeat"
        style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url('{{ asset('bg-campus.jpg') }}');">

        <!-- Navigation -->
        <div class="absolute top-0 right-0 p-6 z-30">
            @if (!Auth::check())
                <a href="{{ route('login') }}"
                    class="bg-white/10 text-white px-6 py-2 rounded-lg border border-white/20 backdrop-blur-sm hover:bg-white/20 transition">
                    Log in
                </a>
            @else
                <a href="{{ route('logout') }}"
                    class="bg-white/10 text-white px-6 py-2 rounded-lg border border-white/20 backdrop-blur-sm hover:bg-white/20 transition">
                    Logout
                </a>
            @endif
        </div>

        <!-- Main Content -->
        <div class="relative z-20 flex items-center justify-center min-h-screen px-6">
            <div class="bg-white/10 border border-white/20 backdrop-blur-lg rounded-3xl p-10 text-center max-w-3xl w-full animate-fade-in shadow-2xl">
                <p class="text-yellow-300 uppercase text-sm tracking-widest mb-4">Selamat Datang</p>
                <img src="{{ asset('LaporSana.png') }}" alt="LaporSana" class="mx-auto mb-6 w-32 md:w-40">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">LaporSana</h1>
                <p class="text-white/90 text-lg mb-10 leading-relaxed">
                    Sistem Manajemen Pelaporan dan Perbaikan Fasilitas Kampus.
                </p>
                <a href="{{ route('login') }}"
                    class="glow-button bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl text-lg">
                    Masuk Sekarang
                </a>
                <p class="text-white/60 text-sm mt-6">Laporkan fasilitas rusak dengan mudah dan cepat</p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-10 left-10 w-12 h-12 border-2 border-yellow-300/50 rotate-45 hidden md:block animate-[float_6s_ease-in-out_infinite]"></div>
        <div class="absolute top-40 right-20 w-10 h-10 bg-yellow-400/30 rounded-full hidden md:block animate-[pulse-glow_5s_ease-in-out_infinite]"></div>
        <div class="absolute bottom-32 left-20 w-8 h-8 bg-white/20 rotate-45 hidden lg:block animate-[float_7s_ease-in-out_infinite]"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/30 via-blue-800/40 to-gray-900/50 z-10"></div>
    </div>
</body>

</html>
