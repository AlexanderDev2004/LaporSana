<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Login - LaporSana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}" />
    <style>
        .loading-spinner {
            display: none;
            margin-left: 10px;
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="flex w-full lg:w-[1000px] rounded-xl overflow-hidden shadow-2xl">
        <!-- LEFT - FORM -->
        <div class="w-full lg:w-1/2 bg-white p-10">
            <div class="mb-6 text-center">
                <a href="{{ url('/') }}" class="text-4xl font-bold text-gray-800">
                    <span class="text-blue-600">Lapor</span><span class="text-yellow-400">Sana</span>
                </a>
                <p class="text-sm text-gray-500 mt-2">Sistem Manajemen Pelaporan Fasilitas Kampus</p>
            </div>

            <h2 class="text-2xl font-semibold text-gray-700 mb-6 text-center">Masuk ke Akun Anda</h2>

            <form action="{{ route('postlogin') }}" method="POST" id="form-login" autocomplete="off">
                @csrf
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-600 mb-1">Username</label>
                    <div class="relative">
                        <input type="text" id="username" name="username"
                            class="form-input w-full px-4 py-2 pr-10 bg-gray-100 rounded border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan username" required />
                        <span class="absolute right-3 top-2.5 text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    <small id="error-username" class="text-red-500 text-xs"></small>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="form-input w-full px-4 py-2 pr-10 bg-gray-100 rounded border border-gray-300 text-gray-900 focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan password" required />
                        <span class="absolute right-3 top-2.5 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <small id="error-password" class="text-red-500 text-xs"></small>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="checkbox" id="remember" name="remember"
                            class="form-checkbox text-blue-600 bg-gray-100 border-gray-300" />
                        <span class="ml-2">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition-all flex justify-center items-center"
                    id="btn-login">
                    Masuk
                    <span class="fas fa-spinner fa-spin loading-spinner ml-2"></span>
                </button>
            </form>
        </div>

        <!-- RIGHT - IMAGE -->
        <div class="hidden lg:block lg:w-1/2 relative">
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-900 via-blue-700 to-blue-800 opacity-80 z-0"></div>
            <img src="{{ asset('LaporSana.png') }}" class="absolute top-8 left-8 w-24 z-10" alt="Logo" />

            <div class="absolute inset-0 flex flex-col items-center justify-center text-white p-10 z-10">
                <h3 class="text-3xl font-bold mb-4">Selamat Datang di LaporSana</h3>
                <p class="text-center text-white/80 text-sm max-w-xs">
                    Laporkan kerusakan fasilitas kampus Anda dengan mudah dan cepat.
                </p>
            </div>

            <!-- Background Pattern (Opsional) -->
            <div class="absolute bottom-10 right-10 w-20 h-20 border-2 border-yellow-400/30 rotate-45 z-10"></div>
        </div>
    </div>

    <!-- JS Library -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        // Login Loading Spinner (opsional)
        $(document).ready(function () {
            $('#form-login').on('submit', function () {
                $('#btn-login').attr('disabled', true);
                $('.loading-spinner').show();
            });
        });
    </script>
</body>

</html>
