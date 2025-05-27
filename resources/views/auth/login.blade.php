<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Pengguna - LaporSana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <style>
        .loading-spinner {
            display: none;
            margin-left: 10px;
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100 whitespace-nowrap">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <a href="{{ url('/') }}" class="text-3xl font-bold text-gray-900"><b>Lapor</b><span class="text-blue-500">Sana</span></a>
            </div>
            <div>
                <p class="text-gray-600 text-center mb-6">Sign in to start your session</p>
                <form action="{{ route('postlogin') }}" method="POST" id="form-login" autocomplete="off">
                    @csrf
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 mb-1">Username</label>
                        <div class="relative">
                            <input type="text" id="username" name="username" class="form-input w-full px-4 py-2 rounded bg-gray-100 text-gray-900 border border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Username" required>
                            <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-user"></i></span>
                        </div>
                        <small id="error-username" class="error-text text-red-500"></small>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="form-input w-full px-4 py-2 rounded bg-gray-100 text-gray-900 border border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Password" required>
                            <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-lock"></i></span>
                        </div>
                        <small id="error-password" class="error-text text-red-500"></small>
                    </div>
                    <div class="flex items-center justify-between mb-6">
                        <label class="inline-flex items-center text-gray-700">
                            <input type="checkbox" id="remember" name="remember" class="form-checkbox text-blue-500 bg-gray-100 border-gray-300">
                            <span class="ml-2">Remember Me</span>
                        </label>
                        <button type="submit" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-center" id="btn-login">
                            Sign In
                            <span class="fas fa-spinner fa-spin loading-spinner ml-2"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        // ...script tetap sama...
    </script>
</body>
</html>
