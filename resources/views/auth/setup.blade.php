<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>WeatherApp - Initial Setup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

    <style>
        .text-error {
            color: #e05252;
            font-size: 0.9rem;
            margin-top: 6px;
            display: block;
        }
        .unit-selector input[type="radio"] {
            display: none;
        }
        .unit-selector label {
            padding: 15px 30px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .unit-selector input[type="radio"]:checked + label {
            border-color: #4A90E2;
            background-color: #f0f7ff;
            color: #4A90E2;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card setup-card p-5 mx-auto" style="max-width: 600px;">
            <div class="text-center mb-4">
                <div class="step-icon"><i class="fas fa-tools fa-2x" style="color:#4A90E2;"></i></div>
                <h2>Almost there, {{ auth()->user()->name }}!</h2>
                <p class="text-muted">Personalize your experience on the WeatherApp.</p>
            </div>

            <form action="{{ route('setup_submit') }}" method="POST" novalidate>
                @csrf

                <div class="mb-4 position-relative">
                    <label for="city_display" class="form-label fw-bold">
                        <i class="fas fa-map-marker-alt me-2"></i>Your city
                    </label>

                    <input type="text" id="city_display" class="form-control @error('city') is-invalid @enderror"
                        placeholder="Search for a city..." value="{{ old('city_name') }}" autocomplete="off" required>

                    <input type="hidden" id="city_id" name="city" value="{{ old('city') }}">

                    <ul id="city-suggestions" class="list-group position-absolute w-100"
                        style="z-index: 1000; max-height: 200px; overflow-y: auto; display:none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </ul>

                    @error('city')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                    <small class="text-muted">We'll use this city to give you the forecast as soon as you open the app.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold d-block"><i class="fas fa-thermometer-half me-2"></i>Temperature Unit</label>
                    <div class="unit-selector d-flex gap-3">
                        <input type="radio" id="celsius" name="temp_unit" value="celsius"
                            {{ old('temp_unit') === 'celsius' ? 'checked' : '' }}>
                        <label for="celsius">°C (Celsius)</label>

                        <input type="radio" id="fahrenheit" name="temp_unit" value="fahrenheit"
                            {{ old('temp_unit') === 'fahrenheit' ? 'checked' : '' }}>
                        <label for="fahrenheit">°F (Fahrenheit)</label>
                    </div>
                    @error('temp_unit')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4 p-3 border rounded bg-white">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notifications" name="notifications" value="1"
                            {{ old('notifications') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="notifications">Climate Change Alerts</label>
                    </div>
                    @error('notifications')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                    <small class="text-muted">We will notify you about storms, frosts, or extreme heat in your city.</small>
                </div>

                <div class="mb-4">
                    <label for="refresh_rate" class="form-label fw-bold">Update Frequency</label>
                    <select id="refresh_rate" name="refresh_rate" class="form-select @error('refresh_rate') is-invalid @enderror">
                        <option value="180" {{ old('refresh_rate') == '180' ? 'selected' : '' }}>Every 3 hours</option>
                        <option value="300" {{ old('refresh_rate', '300') == '300' ? 'selected' : '' }}>Every 5 hours</option>
                        <option value="1440" {{ old('refresh_rate') == '1440' ? 'selected' : '' }}>Per day</option>
                    </select>
                    @error('refresh_rate')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    Finish and go to dashboard <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>

   <script>
        document.addEventListener('DOMContentLoaded', function () {
            const displayInput = document.getElementById('city_display');
            const idInput = document.getElementById('city_id');
            const suggestionsList = document.getElementById('city-suggestions');

            let debounceTimer;

            displayInput.addEventListener('input', function () {
                const query = this.value.trim();

                if (query.length === 0) {
                    idInput.value = '';
                }

                if (query.length < 2) {
                    suggestionsList.style.display = 'none';
                    return;
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetch(`/api/cities?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            suggestionsList.innerHTML = '';

                            if (data.length === 0) {
                                suggestionsList.style.display = 'none';
                                return;
                            }

                            data.forEach(city => {
                                const li = document.createElement('li');
                                li.classList.add('list-group-item', 'list-group-item-action');

                                const displayText = `${city.name}, ${city.state ? city.state + ', ' : ''}${city.country}`;
                                li.textContent = displayText;
                                li.style.cursor = 'pointer';

                                li.addEventListener('mousedown', function (e) {
                                    e.preventDefault();

                                    displayInput.value = displayText;

                                    idInput.value = city.id;

                                    suggestionsList.style.display = 'none';
                                });

                                suggestionsList.appendChild(li);
                            });

                            suggestionsList.style.display = 'block';
                        })
                        .catch(error => console.error('Error fetching cities:', error));
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (!displayInput.contains(e.target) && !suggestionsList.contains(e.target)) {
                    suggestionsList.style.display = 'none';
                }
            });
        });
        </script>
</body>
</html>
