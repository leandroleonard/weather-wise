<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherApp - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="auth-card mx-auto">
                        <div class="auth-logo">
                            <i class="fas fa-cloud-sun"></i>
                            <h2>WeatherApp</h2>
                            <p>Login to your account</p>
                        </div>

                        <form id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input id="email" name="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="your@email" value="{{ old('email') }}" required>
                                </div>
                                @error('email')
                                    <span class="text-error">{{ $message }}</span>
                                @else
                                    <span class="text-error" id="error-email" style="display:none;"></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input id="password" name="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="••••••••" required>
                                </div>
                                @error('password')
                                    <span class="text-error">{{ $message }}</span>
                                @else
                                    <span class="text-error" id="error-password" style="display:none;"></span>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Lembrar-me</label>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <span id="btnSpinner" class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true" style="display:none;"></span>
                                Login
                            </button>
                        </form>
                        <div class="auth-footer">
                            Don't have an account? <a href="{{ route('register') }}">Register</a>
                        </div>
                        <div class="back-link">
                            <a href="{{ route('home') }}">Return to homepage</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(function () {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            function showError(field, msg) {
                $('#error-' + field).text(msg).show();
                $('#' + field).addClass('is-invalid');
            }
            function clearError(field) {
                $('#error-' + field).hide().text('');
                $('#' + field).removeClass('is-invalid');
            }
            function clearAllErrors() {
                ['email', 'password'].forEach(clearError);
            }

            $('#loginForm').on('submit', function (e) {
                clearAllErrors();

                const email = $('#email').val().trim();
                const password = $('#password').val();

                let ok = true;
                if (!email || !emailRegex.test(email)) {
                    showError('email', 'Please provide a valid email address..');
                    ok = false;
                }
                if (!password) {
                    showError('password', 'Enter your password.');
                    ok = false;
                }

                if (!ok) {
                    e.preventDefault();
                    return false;
                }

                $('#submitBtn').prop('disabled', true);
                $('#btnSpinner').show();
                $('#btnText').text('Login...');

                return true;
            });
        });
    </script>

</body>

</html>
