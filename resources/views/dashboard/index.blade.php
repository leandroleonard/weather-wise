<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>WeatherApp - Real-Time Weather</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <i class="fas fa-cloud-sun"></i>
                        WeatherApp
                    </a>
                </div>

                <div class="position-relative me-3" style="min-width: 300px;">
                    <input type="text" id="city_search_display" class="form-control" placeholder="Search for a city..."
                        autocomplete="off" />
                    <input type="hidden" id="city_search_id" name="city_search_id" />
                    <ul id="city_search_suggestions" class="list-group position-absolute w-100"
                        style="z-index: 1000; max-height: 200px; overflow-y: auto; display:none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="user-greeting">Hi, {{ auth()->user()->name }}</span>
                    <button class="btn btn-logout" id="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        @if($city != null)
            <h1 class="city-title">
                Climate in {{ $city->name }}
            </h1>

            @if(isset($alerts) && $alerts->count() > 0)
                <div class="alerts-section mt-4 mb-5">
                    <h2 class="forecast-title">Weather Alerts</h2>

                    <div class="alerts-list d-flex flex-column gap-3 mt-2">
                        @foreach($alerts as $alert)
                            @php
                                $iconClass = 'fas fa-exclamation-triangle';
                                $bgClass = 'alert-warning';
                                $sender = $alert->sender_name ?? 'Official';
                                $start = parseWithOffset($alert->start_time, $offset);
                                $end = parseWithOffset($alert->end_time, $offset);

                                $tags = json_decode($alert->tags ?? '[]', true) ?: [];
                            @endphp

                            <div class="alert-card p-3 border rounded shadow-sm" data-alert-id="{{ $alert->id }}">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="alert-icon">
                                            <i class="{{ $iconClass }} fa-2x text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="alert-title fw-bold text-white" style="font-size:1.05rem;">
                                                {{ $alert->event ?? 'Weather Alert' }}
                                            </div>
                                            <div class="alert-meta text-white" style="font-size:0.9rem;">
                                                {{ $sender }}
                                                @if($start)
                                                    · {{ $start->locale('en')->isoFormat('D [de] MMM [às] HH:mm') }}
                                                @endif
                                                @if($end)
                                                    — {{ $end->locale('en')->isoFormat('D [de] MMM [às] HH:mm') }}
                                                @endif
                                                ·
                                                <small>{{ parseWithOffset($alert->cached_at ?? now(), $offset)?->diffForHumans() }}</small>
                                            </div>

                                            @if(!empty($tags))
                                                <div class="mt-2">
                                                    @foreach($tags as $t)
                                                        <span class="badge bg-secondary me-1">{{ $t }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary btn-toggle-desc"
                                            data-target="#alert-desc-{{ $alert->id }}">
                                            Details
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary btn-dismiss-alert">
                                            Dismiss
                                        </button>
                                    </div>
                                </div>

                                <div class="alert-description mt-3 collapse" id="alert-desc-{{ $alert->id }}">
                                    <div class="small text-muted" style="white-space:pre-wrap;">
                                        {!! nl2br(e($alert->description ?? 'No description provided.')) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="current-weather">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 style="color: #1e293b; font-weight: 700; margin-bottom: 0.5rem;">Now</h2>
                        <div class="current-condition">{{ $current->weather_description ?? 'N/A' }}</div>
                        <div class="current-temp">{{ round($current->temperature) }}°C</div>
                        <div class="feels-like" style="font-size: 1rem;">Sensação: {{ round($current->feels_like) }}°C</div>
                    </div>
                    <div class="col-md-6 text-center">
                        <i class="{{ ow_icon_to_fa($current->weather_icon) }}"
                            style="font-size: 6rem;color: {{ ow_icon_color($current->weather_icon) }};"></i>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <i class="fas fa-thermometer-half"></i>
                        <div class="value">{{ round($current->temperature) }}°C</div>
                        <div class="label">Temperature</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-tint"></i>
                        <div class="value">{{ $current->humidity }}%</div>
                        <div class="label">Humidity</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-wind"></i>
                        <div class="value">{{ round($current->wind_speed * 3.6) }} km/h</div>
                        <div class="label">Wind</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-cloud-rain"></i>
                        <div class="value">{{ round(($current->pop ?? 0) * 100) }}%</div>
                        <div class="label">Precipitation</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-eye"></i>
                        <div class="value">{{ round($current->visibility / 1000) }} km</div>
                        <div class="label">Visibility</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-compress-arrows-alt"></i>
                        <div class="value">{{ $current->pressure }} hPa</div>
                        <div class="label">Pressure</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-sunrise"></i>
                        <div class="value">{{ parseWithOffset($current->sunrise, $offset)?->format('H:i') }}</div>
                        <div class="label">Sunrise</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-sunset"></i>
                        <div class="value">{{ parseWithOffset($current->sunset, $offset)?->format('H:i') }}</div>
                        <div class="label">Sunset</div>
                    </div>
                </div>
            </div>

            <div class="hourly-forecast-section mt-4">
                <h2 class="forecast-title">Today's Time Forecast</h2>
                <div class="hourly-forecast-cards d-flex overflow-auto gap-3 pb-2">
                    @foreach($hourly as $hour)
                        @php if($hour->dt < \Carbon\Carbon::now()->subHour()) continue; @endphp
                        <div class="hourly-forecast-card text-center p-3 border rounded"
                            style="min-width: 100px; background: #f8fafc;">
                            <div class="hourly-time fw-bold">
                                {{ parseWithOffset($hour->dt, $offset)?->format('H:i') }}
                            </div>
                            <div class="hourly-icon my-2">
                                <i class="{{ ow_icon_to_fa($hour->weather_icon) }} fa-2x"
                                    style="color: {{ ow_icon_color($hour->weather_icon) }}"></i>
                            </div>
                            <div class="hourly-temp fw-semibold">
                                {{ round($hour->temperature) }}°
                            </div>
                            <div class="hourly-feels-like text-muted" style="font-size: 0.85rem;">
                                Feels like: {{ round($hour->feels_like) }}°
                            </div>
                            <div class="hourly-pop mt-1" style="font-size: 0.8rem; color: #3b82f6;">
                                {{ round(($hour->pop ?? 0) * 100) }}% Rain
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


            <div class="forecast-section">
                <h2 class="forecast-title">Forecast for the Next 5 Days</h2>
                <div class="forecast-cards">
                    @foreach($daily as $day)
                        <div class="forecast-card">
                            <div class="forecast-day">
                                {{ parseWithOffset($day->forecast_date, $offset)?->locale('en')->isoFormat('ddd') }}
                            </div>
                            <div class="forecast-date">
                                {{ parseWithOffset($day->forecast_date, $offset)?->format('d/m') }}
                            </div>
                            <div class="forecast-icon">
                                <i class="{{ ow_icon_to_fa($day->weather_icon) }}"
                                    style="font-size: 3rem;color: {{ ow_icon_color($day->weather_icon) }};"></i>
                            </div>
                            <div class="forecast-temp">{{ round($day->temp_day) }}°</div>
                            <div class="forecast-temp-range">{{ round($day->temp_min) }}°</div>
                            <div class="forecast-rain">{{ round(($day->pop ?? 0) * 100) }}% Rain</div>
                        </div>
                    @endforeach
                </div>
            </div>

        @else

        @endif


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#btn-logout').on('click', function (e) {
                e.preventDefault();
                if (confirm("Are you sure you want to leave?")) {
                    $.post('{{ route("logout") }}', function (data) {
                        window.location.href = '{{ route("home") }}';
                    }).fail(function () {
                        alert("Error logging out.");
                    });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const options = {
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: true,
                        type: 'x',
                        autoScaleYaxis: true
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                series: [
                    {
                        name: 'Temperature (°C)',
                        data: [
                            @foreach($hourly as $h)
                                {{ round($h->temperature) }},
                            @endforeach
                    ]
                    },
                    {
                        name: 'Feels Like (°C)',
                        data: [
                            @foreach($hourly as $h)
                                {{ round($h->feels_like) }},
                            @endforeach
                    ]
                    }
                ],
                xaxis: {
                    categories: [
                        @foreach($hourly as $h)
                            '{{ \Carbon\Carbon::parse($h->dt)->format('H:i') }}',
                        @endforeach
                ],
                    title: {
                        text: 'Hour'
                    },
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Temperature (°C)'
                    },
                    min: Math.min(
                        @foreach($hourly as $h)
                            {{ round($h->temperature) }},
                        @endforeach
                ) - 5,
                    max: Math.max(
                        @foreach($hourly as $h)
                            {{ round($h->temperature) }},
                        @endforeach
                ) + 5,
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return val + "°C";
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                colors: ['#1E90FF', '#FF6347']
            };

            const chart = new ApexCharts(document.querySelector("#hourlyChart"), options);
            chart.render();
        });

        $(function () {
            $('.btn-toggle-desc').on('click', function () {
                const target = $(this).data('target');
                $(target).collapse('toggle');
            });

            $('.btn-dismiss-alert').on('click', function () {
                const $card = $(this).closest('.alert-card');
                $card.fadeOut(250, function () { $(this).remove(); });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const displayInput = document.getElementById('city_search_display');
            const idInput = document.getElementById('city_search_id');
            const suggestionsList = document.getElementById('city_search_suggestions');

            let debounceTimer;

            displayInput.addEventListener('input', function () {
                const query = this.value.trim();

                if (query.length === 0) {
                    idInput.value = '';
                    suggestionsList.style.display = 'none';
                    return;
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

                                    window.location.href = `/dashboard/city/${city.id}/weather`;
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
