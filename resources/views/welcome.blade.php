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
            <div
                class="bg-white/10 border border-white/20 backdrop-blur-lg rounded-3xl p-10 text-center max-w-3xl w-full shadow-2xl">
                <p class="text-yellow-300 uppercase text-sm tracking-widest mb-4">Selamat Datang</p>
                <img src="{{ asset('LaporSana.png') }}" alt="LaporSana" class="mx-auto mb-6 w-32 md:w-40 drop-shadow-lg">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">LaporSana</h1>
                <p class="text-white/90 text-lg mb-10 leading-relaxed">
                    Sistem Manajemen Pelaporan dan Perbaikan Fasilitas Kampus.
                </p>
                <a href="{{ route('login') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl text-lg shadow-lg transition transform hover:scale-105">
                    Masuk Sekarang
                </a>
                <p class="text-white/60 text-sm mt-6">Laporkan fasilitas rusak dengan mudah dan cepat</p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-10 left-10 w-12 h-12 border-2 border-yellow-300/50 rotate-45 hidden md:block animate-bounce"></div>
        <div class="absolute top-40 right-20 w-10 h-10 bg-yellow-400/30 rounded-full hidden md:block animate-pulse"></div>
        <div class="absolute bottom-32 left-20 w-8 h-8 bg-white/20 rotate-45 hidden lg:block animate-bounce"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/30 via-blue-800/40 to-gray-900/50 z-10"></div>
    </div>

    <!-- Tentang LaporSana -->
    <section class="relative z-30 py-20 bg-gray-900 text-white">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-4 text-yellow-300">Apa itu LaporSana?</h2>
            <p class="text-white/80 text-lg leading-relaxed">
                <strong>LaporSana</strong> adalah sistem pelaporan fasilitas kampus yang dirancang untuk memudahkan mahasiswa, dosen, dan tenaga kependidikan dalam melaporkan kerusakan fasilitas kampus secara cepat dan efisien. Dengan sistem ini, setiap laporan akan ditindaklanjuti secara sistematis hingga selesai.
            </p>
        </div>
    </section>

    <!-- Fitur Section -->
    <section class="relative z-30 py-16 bg-gray-950">
        <div class="max-w-5xl mx-auto px-6">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-12 text-yellow-300">Fitur Utama</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Fitur 1 -->
                <div
                    class="bg-gray-800/80 rounded-2xl p-6 text-center shadow-lg hover:shadow-yellow-200/20 transition-transform hover:-translate-y-1 duration-300">
                    <div class="flex justify-center mb-4">
                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Pelaporan Mudah</h3>
                    <p class="text-white/70 text-sm">Laporkan fasilitas rusak dengan formulir online, kapan saja di mana saja.</p>
                </div>
                <!-- Fitur 2 -->
                <div
                    class="bg-gray-800/80 rounded-2xl p-6 text-center shadow-lg hover:shadow-yellow-200/20 transition-transform hover:-translate-y-1 duration-300">
                    <div class="flex justify-center mb-4">
                        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-2a4 4 0 014-4h4m0 0V7a4 4 0 00-4-4H7a4 4 0 00-4 4v10a4 4 0 004 4h4" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Pantau Status</h3>
                    <p class="text-white/70 text-sm">Cek perkembangan laporan dan status perbaikan secara real-time.</p>
                </div>
                <!-- Fitur 3 -->
                <div
                    class="bg-gray-800/80 rounded-2xl p-6 text-center shadow-lg hover:shadow-yellow-200/20 transition-transform hover:-translate-y-1 duration-300">
                    <div class="flex justify-center mb-4">
                        <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Tindak Lanjut Cepat</h3>
                    <p class="text-white/70 text-sm">Tim kampus segera menindaklanjuti setiap laporan kerusakan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cara Kerja -->
    <section class="relative z-30 py-20 bg-gray-900">
        <div class="max-w-6xl mx-auto px-6 text-white text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-yellow-300 mb-10">Bagaimana Cara Kerjanya?</h2>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="p-6 bg-gray-800/70 rounded-xl">
                    <h3 class="font-bold text-lg mb-2">1. Laporkan</h3>
                    <p class="text-white/70 text-sm">Isi formulir laporan dengan detail dan bukti kerusakan.</p>
                </div>
                <div class="p-6 bg-gray-800/70 rounded-xl">
                    <h3 class="font-bold text-lg mb-2">2. Verifikasi</h3>
                    <p class="text-white/70 text-sm">Admin memverifikasi laporan dan memproses tingkat urgensinya.</p>
                </div>
                <div class="p-6 bg-gray-800/70 rounded-xl">
                    <h3 class="font-bold text-lg mb-2">3. Tindak Lanjut</h3>
                    <p class="text-white/70 text-sm">Tim teknis ditugaskan untuk melakukan perbaikan.</p>
                </div>
                <div class="p-6 bg-gray-800/70 rounded-xl">
                    <h3 class="font-bold text-lg mb-2">4. Selesai</h3>
                    <p class="text-white/70 text-sm">Status laporan diperbarui hingga perbaikan selesai.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-6 text-center text-sm border-t border-white/10">
        <p>&copy; <script>document.write(new Date().getFullYear())</script> LaporSana. Politeknik Negeri Malang. All rights reserved.</p>
    </footer>
</body>

</html>
