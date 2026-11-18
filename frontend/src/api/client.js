import axios from 'axios'

// ---------- 1. Where the login token is kept ----------
// The token is saved in the browser (localStorage), so the user stays
// signed in after a page refresh.
const TOKEN_KEY = 'quasario_token'

export const tokenStorage = {
  get: () => localStorage.getItem(TOKEN_KEY),
  set: (token) => localStorage.setItem(TOKEN_KEY, token),
  clear: () => localStorage.removeItem(TOKEN_KEY),
}

// ---------- 2. One axios object for the whole site ----------
// Every request starts with /api/v1 and asks Laravel for JSON.
export const api = axios.create({
  baseURL: '/api/v1',
  headers: { Accept: 'application/json' },
})

// Before each request: add the token (if we have one).
api.interceptors.request.use((config) => {
  const token = tokenStorage.get()
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// ---------- 3. When the session ends by itself ----------
// The AuthProvider (step 4) gives us a function to call here.
let whenSessionEnds = () => {}

export function onSessionEnded(handler) {
  whenSessionEnds = handler
}

// After each answer: if the token is no longer valid (password changed,
// signed out on another device) or the account is banned, sign out here too.
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const code = error.response?.data?.code
    const hadToken = Boolean(tokenStorage.get())

    if (hadToken && (code === 'unauthenticated' || code === 'accountBanned')) {
      tokenStorage.clear()
      whenSessionEnds(code)
    }

    return Promise.reject(error)
  },
)

// ---------- 4. Read any error in one simple shape ----------
// Returns { code, fields }.
// code   = 'invalidCredentials', 'validationFailed', 'networkError' ...
// fields = { email: 'alreadyUsed', password: 'weakPassword' } (only for 422)
export function readApiError(error) {
  const data = error.response?.data

  if (!data) {
    return { code: 'networkError', fields: {} }
  }

  const fields = {}
  for (const [name, codes] of Object.entries(data.errors ?? {})) {
    fields[name] = codes[0]
  }

  return { code: data.code ?? 'unknownError', fields }
}