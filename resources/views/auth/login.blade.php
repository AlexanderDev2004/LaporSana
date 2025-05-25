<!DOCTYPE html>
<html lang="en" class="dark">

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

<body class="min-h-screen flex items-center justify-center bg-gray-900 dark:bg-gray-900">
    <div class="w-full max-w-md">
        <div class="bg-gray-800 rounded-lg shadow-lg p-8">
            <div class="text-center mb-6">
                <a href="{{ url('/') }}" class="text-3xl font-bold text-white"><b>Lapor</b><span class="text-blue-400">Sana</span></a>
            </div>
            <div>
                <p class="text-gray-300 text-center mb-6">Sign in to start your session</p>
                <form action="{{ route('postlogin') }}" method="POST" id="form-login" autocomplete="off">
                    @csrf
                    <div class="mb-4">
                        <label for="username" class="block text-gray-300 mb-1">Username</label>
                        <div class="relative">
                            <input type="text" id="username" name="username" class="form-input w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:border-blue-500 focus:ring-blue-500" placeholder="Username" required>
                            <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-user"></i></span>
                        </div>
                        <small id="error-username" class="error-text text-red-400"></small>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-300 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="form-input w-full px-4 py-2 rounded bg-gray-700 text-white border border-gray-600 focus:border-blue-500 focus:ring-blue-500" placeholder="Password" required>
                            <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-lock"></i></span>
                        </div>
                        <small id="error-password" class="error-text text-red-400"></small>
                    </div>
                    <div class="flex items-center justify-between mb-6">
                        <label class="inline-flex items-center text-gray-300">
                            <input type="checkbox" id="remember" name="remember" class="form-checkbox text-blue-500 bg-gray-700 border-gray-600">
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $("#form-login").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    password: {
                        required: true,
                        minlength: 6,
                        maxlength: 20
                    }
                },
                submitHandler: function(form) {
                    const $btnLogin = $('#btn-login');
                    $btnLogin.prop('disabled', true);
                    $btnLogin.find('.loading-spinner').show();

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            $('.error-text').text('');
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                }).then(function() {
                                    window.location = response.redirect;
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'
                            });
                        },
                        complete: function() {
                            $btnLogin.prop('disabled', false);
                            $btnLogin.find('.loading-spinner').hide();
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>
</html>
