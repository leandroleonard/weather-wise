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

      <div v-if="errors?.name" class="alert">{{ errors.name }}</div>
      <div v-if="errors?.email" class="alert">{{ errors.email }}</div>
      <div v-if="errors?.password" class="alert">{{ errors.password }}</div>
      <div v-if="flash?.error" class="alert">{{ flash.error }}</div>

      <form @submit.prevent="submit" class="auth-form">
        <label class="form-label">Name</label>
        <div class="input-wrapper">
          <i class="fas fa-user input-icon"></i>
          <input
            v-model="form.name"
            type="text"
            class="input"
            placeholder="Seu nome"
            :class="{ 'is-invalid': errors?.name }"
            required
          />
        </div>

        <label class="form-label">Email</label>
        <div class="input-wrapper">
          <i class="fas fa-envelope input-icon"></i>
          <input
            v-model="form.email"
            type="email"
            class="input"
            placeholder="seu@email.com"
            :class="{ 'is-invalid': errors?.email }"
            required
          />
        </div>

        <label class="form-label">Password</label>
        <div class="input-wrapper">
          <i class="fas fa-lock input-icon"></i>
          <input
            v-model="form.password"
            type="password"
            class="input"
            placeholder="••••••••"
            :class="{ 'is-invalid': errors?.password }"
            required
          />
        </div>

        <label class="form-label">Confirm Passowrd</label>
        <div class="input-wrapper">
          <i class="fas fa-lock input-icon"></i>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="input"
            placeholder="••••••••"
            required
          />
        </div>

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
import { ref } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'

const page = usePage()
const errors = page?.props?.value?.errors ?? {}
const flash = page?.props?.value?.flash ?? {}

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const processing = ref(false)

function submit() {
  processing.value = true
  form.post('/register', {
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>
