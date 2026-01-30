<template>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-top">
                <div class="auth-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <h2 class="auth-title">WeatherApp</h2>
                <p class="auth-subtitle">Create your account</p>
            </div>

            <div v-if="serverErrors?.name" class="alert">{{ serverErrors.name }}</div>
            <div v-if="serverErrors?.email" class="alert">{{ serverErrors.email }}</div>
            <div v-if="serverErrors?.password" class="alert">{{ serverErrors.password }}</div>
            <div v-if="flash?.error" class="alert">{{ flash.error }}</div>

            <div v-if="clientErrors.general" class="alert">{{ clientErrors.general }}</div>

            <form @submit.prevent="submit" class="auth-form" novalidate>
                <label class="form-label">Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input v-model="form.name" type="text" class="input" placeholder="Seu nome"
                        :class="{ 'is-invalid': serverErrors?.name || clientErrors.name }" required />
                </div>
                <p v-if="clientErrors.name" class="text-error">{{ clientErrors.name }}</p>

                <label class="form-label">Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input v-model="form.email" type="email" class="input" placeholder="seu@email.com"
                        :class="{ 'is-invalid': serverErrors?.email || clientErrors.email }" required />
                </div>
                <p v-if="clientErrors.email" class="text-error">{{ clientErrors.email }}</p>

                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input v-model="form.password" type="password" class="input" placeholder="••••••••"
                        :class="{ 'is-invalid': serverErrors?.password || clientErrors.password }" required
                        @input="onPasswordInput" />
                </div>
                <p v-if="clientErrors.password" class="text-error">{{ clientErrors.password }}</p>

                <div v-if="form.password.length" class="password-strength">
                    <div class="password-strength-text" :style="{ color: passwordStrength.color, fontSize: '12px' }">
                        {{ passwordStrength.text }}
                    </div>
                    <div class="password-strength-bar"
                        style="background: #e6e9ef; border-radius: 8px; height: 1.5px; overflow:hidden; margin-top:1px;">
                        <div
                            :style="{ width: passwordStrength.pct + '%', background: passwordStrength.color, height: '100%', transition: 'width .2s' }">
                        </div>
                    </div>
                </div>

                <label class="form-label">Confirm Passowrd</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input v-model="form.password_confirmation" type="password" class="input" placeholder="••••••••"
                        :class="{ 'is-invalid': clientErrors.password_confirmation }" required />
                </div>
                <p v-if="clientErrors.password_confirmation" class="text-error">{{ clientErrors.password_confirmation }}
                </p>

                <button type="submit" class="btn-primary" :disabled="processing">
                    <span v-if="processing" class="spinner" aria-hidden="true"></span>
                    Register
                </button>
            </form>

            <div class="auth-footer">
                Do you already have an account? <Link href="/login" class="link">Login</Link>
            </div>

            <div class="back-link">
                <Link href="/" class="link-muted">Return to homepage</Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'


const page = usePage()
const serverErrors = page?.props?.value?.errors ?? {}
const flash = page?.props?.value?.flash ?? {}

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const processing = ref(false)

const clientErrors = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    general: ''
})

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/

function clearClientErrors() {
    clientErrors.name = ''
    clientErrors.email = ''
    clientErrors.password = ''
    clientErrors.password_confirmation = ''
    clientErrors.general = ''
}


const passwordStrength = computed(() => {
    const p = form.password || ''
    if (p.length === 0) return { text: '', pct: 0, color: '#cbd5e1' }
    let score = 0
    if (p.length >= 8) score++
    if (/[A-Z]/.test(p)) score++
    if (/[a-z]/.test(p)) score++
    if (/\d/.test(p)) score++
    if (/[^\w\s]/.test(p)) score++

    const pct = Math.min(100, Math.round((score / 5) * 100))
    let text = 'Very weak'
    let color = '#ef4444'
    if (score >= 4) { text = 'Strong'; color = '#10b981' }
    else if (score === 3) { text = 'Average'; color = '#f59e0b' }
    else if (score === 2) { text = 'Weak'; color = '#fb923c' }
    return { text, pct, color }
})

function onPasswordInput() {
    if (clientErrors.password_confirmation) clientErrors.password_confirmation = ''
    if (clientErrors.password) clientErrors.password = ''
}


function validateClient() {
    clearClientErrors()
    let ok = true

    if (!form.name || form.name.trim().length < 2) {
        clientErrors.name = 'Please provide your name (minimum 2 characters).'
        ok = false
    }

    if (!form.email || !emailRegex.test(form.email)) {
        clientErrors.email = 'Please provide a valid email address.'
        ok = false
    }

    if (!form.password || !strongPasswordRegex.test(form.password)) {
        clientErrors.password = 'Weak password. Use at least 8 characters, including uppercase, lowercase, numbers, and special characters.'
        ok = false
    }

    if (form.password !== form.password_confirmation) {
        clientErrors.password_confirmation = "The passwords don't match."
        ok = false
    }

    return ok
}

function submit() {
    if (!validateClient()) return

    processing.value = true
    form.post('/register', {
        onError: (errors) => {
            Object.keys(errors).forEach((k) => {
                clientErrors[k] = Array.isArray(errors[k]) ? errors[k].join(' ') : errors[k]
            })
        },
        onFinish: () => {
            processing.value = false
        }
    })
}
</script>

<style scoped>
.password-strength {
    margin-bottom: 10px;
}

.password-strength-text {
    font-size: 13px;
    font-weight: 600;
}
</style>
