<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherApp - Real-Time Weather</title>
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
                <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-light">Register</a>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1>Real-Time Weather</h1>
            <p>Follow the weather conditions in the world's major cities.</p>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row">
            @foreach($featuredCities as $city)
                @php
                    $weather = $city->weather;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="weather-card">
                        <div class="city-name">{{ $city->name }}</div>
                        <div class="country">{{ $city->country ?? 'N/A' }}</div>
                        <div class="weather-icon">
                            <i class="{{ ow_icon_to_fa($weather->weather_icon ?? '') }}"
                                style="color: {{ ow_icon_color($weather->weather_icon ?? '') }};"></i>
                        </div>
                        <div class="temperature">{{ round($weather->temperature ?? 0) }}°C</div>
                        <div class="weather-description">{{ $weather->weather_description ?? 'N/A' }}</div>
                        <div class="feels-like">Feels like: {{ round($weather->feels_like ?? 0) }}°C</div>
                        <div class="weather-details">
                            <div class="detail-item">
                                <i class="fas fa-tint"></i>
                                <div class="detail-value">{{ $weather->humidity ?? 0 }}%</div>
                                <div class="detail-label">Humidity</div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-wind"></i>
                                <div class="detail-value">{{ round(($weather->wind_speed ?? 0) * 3.6) }} km/h</div>
                                <div class="detail-label">Wind</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

    </script>
</body>

</html>
