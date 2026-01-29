<template>
    <div class="main-content">
        <div class="dashboard-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a class="navbar-brand" href="#">
                            <i class="fas fa-cloud-sun"></i>
                            WeatherApp
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="user-greeting">Olá, {{ user?.name || 'usuário' }}!</span>
                        <form :action="routeLogout" method="post" @submit.prevent="logout">
                            <button type="submit" class="btn btn-logout">
                                <i class="fas fa-sign-out-alt"></i> Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <h1 class="city-title">Clima em {{ cityName }}</h1>

            <!-- Clima Atual -->
            <div class="current-weather">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 style="color: #1e293b; font-weight: 700; margin-bottom: 0.5rem;">Agora</h2>
                        <div class="current-condition">{{ weather?.weather_description || '—' }}</div>
                        <div class="current-temp">{{ weather?.temperature ? weather.temperature + '°C' : '—' }}</div>
                        <div class="feels-like" style="font-size: 1rem;">Sensação: {{ weather?.feels_like ?
                            weather.feels_like + '°C' : '—' }}</div>
                    </div>
                    <div class="col-md-6 text-center">
                        <i :class="weatherIconClass" style="font-size: 6rem; color: #94a3b8;"></i>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <i class="fas fa-thermometer-half"></i>
                        <div class="value">{{ weather?.temperature ? weather.temperature + '°C' : '—' }}</div>
                        <div class="label">Temperatura</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-tint"></i>
                        <div class="value">{{ weather?.humidity ? weather.humidity + '%' : '—' }}</div>
                        <div class="label">Umidade</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-wind"></i>
                        <div class="value">{{ weather?.wind_speed ? weather.wind_speed + ' km/h' : '—' }}</div>
                        <div class="label">Vento</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-cloud-rain"></i>
                        <div class="value">{{ weather?.pop ? Math.round(weather.pop * 100) + '%' : '—' }}</div>
                        <div class="label">Precipitação</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-eye"></i>
                        <div class="value">{{ weather?.visibility ? (weather.visibility / 1000) + ' km' : '—' }}</div>
                        <div class="label">Visibilidade</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-compress-arrows-alt"></i>
                        <div class="value">{{ weather?.pressure ? weather.pressure + ' hPa' : '—' }}</div>
                        <div class="label">Pressão</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-sunrise"></i>
                        <div class="value">{{ sunrise }}</div>
                        <div class="label">Nascer do Sol</div>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-sunset"></i>
                        <div class="value">{{ sunset }}</div>
                        <div class="label">Pôr do Sol</div>
                    </div>
                </div>
            </div>

            <!-- Previsão 5 Dias -->
            <div class="forecast-section">
                <h2 class="forecast-title">Previsão para os Próximos 5 Dias</h2>
                <div class="forecast-cards">
                    <div v-for="day in forecast" :key="day.forecast_date" class="forecast-card">
                        <div class="forecast-day">{{ day.day_name }}</div>
                        <div class="forecast-date">{{ day.forecast_date }}</div>
                        <div class="forecast-icon">
                            <i :class="day.icon_class" :style="{ color: '#3b82f6' }"></i>
                        </div>
                        <div class="forecast-temp">{{ day.temp_max }}°</div>
                        <div class="forecast-temp-range">{{ day.temp_min }}°</div>
                        <div class="forecast-rain">{{ day.pop_pct }}% chuva</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'

const page = usePage()
const props = page.props.value || {}

const user = props.auth?.user ?? null
// Se seu backend passar city/weather via props, use-os
const cityName = props.city?.name ?? props.user?.city ?? 'São Paulo'
const weather = props.weather ?? null
const forecast = props.forecast ?? []

const routeLogout = '/logout' // ajuste para sua rota de logout

function logout(e) {
    e.preventDefault()
    Inertia.post(routeLogout)
}

// helpers para exibir sunrise/sunset (assume valores em string/hora já prontos, se timestamp, converta)
const sunrise = weather?.sunrise_time ?? '—'
const sunset = weather?.sunset_time ?? '—'

// simples mapping de ícones — ajuste conforme seu backend fornece icon/class
const weatherIconClass = weather?.weather_icon_class ?? 'fas fa-cloud'
</script>
