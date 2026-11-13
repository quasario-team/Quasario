import axios from 'axios'

// One axios object for the whole site. Every request starts with this address.
const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/v1',
  headers: { Accept: 'application/json' },
})

// Before every request: if a token is saved, send it to Laravel.
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api
