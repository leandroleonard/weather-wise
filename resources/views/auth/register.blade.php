<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherApp - Registration</title>
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
                            <p>Create an account and follow the weather.</p>
                        </div>

                        {{-- @if(session('flash.error'))
                            <div class="alert alert-danger" id="serverFlash">{{ session('flash.error') }}</div>
                        @endif

                        @if ($errors->any() && old('_from_js') !== '1')
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}


                        <form id="signupForm" action="{{ route('register') }}" method="POST" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <div class="input-group">
                                    <i class="fas fa-user input-icon"></i>
                                    <input id="name" name="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="Seu nome" required>
                                </div>
                                @error('name')
                                    <span class="text-error">{{ $message }}</span>
                                @else
                                    <span class="text-error" id="error-name" style="display:none"></span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="seu@email.com" required>
                                </div>
                                @error('email')
                                    <span class="text-error">{{ $message }}</span>
                                @else
                                    <span class="text-error" id="error-email" style="display:none"></span>
                                @enderror
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="••••••••" required>
                                </div>
                                <small class="text-muted">Minimum of 6 characters</small>

                                <div class="password-strength mt-2" style="display:none;">
                                    <div id="passwordStrengthText" class="small fw-bold"></div>
                                    <div class="password-strength-bar">
                                    <div id="passwordStrengthFill" class="password-strength-fill"></div>
                                    </div>
                                </div>

                                @error('password')
                                    <span class="text-error">{{ $message }}</span>
                                @else
                                    <span class="text-error" id="error-password" style="display:none"></span>
                                @enderror

                            </div>


                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="form-control" placeholder="••••••••" required>
                                </div>

                                <span class="text-error" id="error-password_confirmation" style="display:none;"></span>
                            </div>


                            <button id="submitBtn" type="submit" class="btn btn-primary w-100">
                                <span id="btnSpinner" class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true" style="display:none;"></span>
                                <span id="btnText">Register</span>
                            </button>
                        </form>
                        <div class="auth-footer">
                            Do you already have an account? <a href="{{ route('login') }}">Login</a>
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
            const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/;

            function showError(field, msg) {
                $('#error-' + field).text(msg).show();
                $('#' + field).addClass('is-invalid');
            }
            function clearError(field) {
                $('#error-' + field).hide().text('');
                $('#' + field).removeClass('is-invalid');
            }
            function clearAllErrors() {
                ['name', 'email', 'password', 'password_confirmation'].forEach(clearError);
            }

            function evaluatePassword(pw) {
                if (!pw) return { text: '', pct: 0, color: '#e6e9ef' };
                let score = 0;
                if (pw.length >= 8) score++;
                if (/[A-Z]/.test(pw)) score++;
                if (/[a-z]/.test(pw)) score++;
                if (/\d/.test(pw)) score++;
                if (/[^\w\s]/.test(pw)) score++;
                const pct = Math.round((score / 5) * 100);
                let text = 'Muito fraca', color = '#ef4444';
                if (score >= 4) { text = 'Forte'; color = '#10b981'; }
                else if (score === 3) { text = 'Média'; color = '#f59e0b'; }
                else if (score === 2) { text = 'Fraca'; color = '#fb923c'; }
                return { text, pct, color };
            }

            $('#password').on('input', function () {
                const pw = $(this).val();
                const res = evaluatePassword(pw);
                if (pw.length === 0) {
                    $('.password-strength').hide();
                } else {
                    $('.password-strength').show();
                    $('#passwordStrengthText').text(res.text).css('color', res.color);
                    $('#passwordStrengthFill').css({ width: res.pct + '%', background: res.color });
                }
                clearError('password_confirmation');
            });

            $('#signupForm').on('submit', function (e) {
                clearAllErrors();

                const name = $('#name').val().trim();
                const email = $('#email').val().trim();
                const password = $('#password').val();
                const password_confirmation = $('#password_confirmation').val();

                let ok = true;
                if (!name || name.length < 2) {
                    showError('name', 'Informe seu nome (mínimo 2 caracteres).');
                    ok = false;
                }
                if (!email || !emailRegex.test(email)) {
                    showError('email', 'Informe um e-mail válido.');
                    ok = false;
                }
                if (!password || !strongPasswordRegex.test(password)) {
                    showError('password', 'Senha fraca. Use ao menos 8 caracteres, incluindo maiúscula, minúscula, número e caracter especial.');
                    ok = false;
                }
                if (password !== password_confirmation) {
                    showError('password_confirmation', 'As senhas não coincidem.');
                    ok = false;
                }

                if (!ok) {
                    e.preventDefault();
                    return false;
                }

                $('#submitBtn').prop('disabled', true);
                $('#btnSpinner').show();
                $('#btnText').text('Criando...');
                return true;
            });
        });
    </script>
</body>

</html>
