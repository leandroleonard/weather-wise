<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherApp - Clima em Tempo Real</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#" onclick="showPage('homePage')">
                <i class="fas fa-cloud-sun"></i>
                WeatherApp
            </a>
            <div>
                <a href="{{ route('login') }}" class="btn btn-outline-light me-2" >Entrar</a>
                <a href="{{ route('register') }}" class="btn btn-light">Cadastrar</a>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1>Clima em Tempo Real</h1>
            <p>Acompanhe as condições climáticas das principais cidades do mundo</p>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row">
            <!-- São Paulo -->
            <div class="col-md-6 col-lg-4">
                <div class="weather-card">
                    <div class="city-name">São Paulo</div>
                    <div class="country">Brasil</div>
                    <div class="weather-icon">
                        <i class="fas fa-cloud" style="color: #94a3b8;"></i>
                    </div>
                    <div class="temperature">24°C</div>
                    <div class="weather-description">Parcialmente Nublado</div>
                    <div class="feels-like">Sensação térmica: 25°C</div>
                    <div class="weather-details">
                        <div class="detail-item">
                            <i class="fas fa-tint"></i>
                            <div class="detail-value">65%</div>
                            <div class="detail-label">Umidade</div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-wind"></i>
                            <div class="detail-value">15 km/h</div>
                            <div class="detail-label">Vento</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rio de Janeiro -->
            <div class="col-md-6 col-lg-4">
                <div class="weather-card">
                    <div class="city-name">Rio de Janeiro</div>
                    <div class="country">Brasil</div>
                    <div class="weather-icon">
                        <i class="fas fa-sun" style="color: #fbbf24;"></i>
                    </div>
                    <div class="temperature">28°C</div>
                    <div class="weather-description">Ensolarado</div>
                    <div class="feels-like">Sensação térmica: 30°C</div>
                    <div class="weather-details">
                        <div class="detail-item">
                            <i class="fas fa-tint"></i>
                            <div class="detail-value">70%</div>
                            <div class="detail-label">Umidade</div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-wind"></i>
                            <div class="detail-value">12 km/h</div>
                            <div class="detail-label">Vento</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Londres -->
            <div class="col-md-6 col-lg-4">
                <div class="weather-card">
                    <div class="city-name">Londres</div>
                    <div class="country">Reino Unido</div>
                    <div class="weather-icon">
                        <i class="fas fa-cloud-rain" style="color: #3b82f6;"></i>
                    </div>
                    <div class="temperature">12°C</div>
                    <div class="weather-description">Chuva Leve</div>
                    <div class="feels-like">Sensação térmica: 10°C</div>
                    <div class="weather-details">
                        <div class="detail-item">
                            <i class="fas fa-tint"></i>
                            <div class="detail-value">85%</div>
                            <div class="detail-label">Umidade</div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-wind"></i>
                            <div class="detail-value">20 km/h</div>
                            <div class="detail-label">Vento</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nova York -->
            <div class="col-md-6 col-lg-4">
                <div class="weather-card">
                    <div class="city-name">Nova York</div>
                    <div class="country">EUA</div>
                    <div class="weather-icon">
                        <i class="fas fa-cloud" style="color: #94a3b8;"></i>
                    </div>
                    <div class="temperature">18°C</div>
                    <div class="weather-description">Nublado</div>
                    <div class="feels-like">Sensação térmica: 16°C</div>
                    <div class="weather-details">
                        <div class="detail-item">
                            <i class="fas fa-tint"></i>
                            <div class="detail-value">60%</div>
                            <div class="detail-label">Umidade</div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-wind"></i>
                            <div class="detail-value">18 km/h</div>
                            <div class="detail-label">Vento</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tóquio -->
            <div class="col-md-6 col-lg-4">
                <div class="weather-card">
                    <div class="city-name">Tóquio</div>
                    <div class="country">Japão</div>
                    <div class="weather-icon">
                        <i class="fas fa-sun" style="color: #fbbf24;"></i>
                    </div>
                    <div class="temperature">22°C</div>
                    <div class="weather-description">Céu Limpo</div>
                    <div class="feels-like">Sensação térmica: 23°C</div>
                    <div class="weather-details">
                        <div class="detail-item">
                            <i class="fas fa-tint"></i>
                            <div class="detail-value">55%</div>
                            <div class="detail-label">Umidade</div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-wind"></i>
                            <div class="detail-value">10 km/h</div>
                            <div class="detail-label">Vento</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

    </script>
</body>

</html>
