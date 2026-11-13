import { useState } from 'react'
import api from '../api'
import { formError } from '../errors'
import AuthLayout from '../components/AuthLayout'
import Field from '../components/Field'

export default function LoginPage({ onLoggedIn, goToRegister }) {
  const [login, setLogin] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  async function handleSubmit(event) {
    event.preventDefault()
    setError('')
    setLoading(true)
    try {
      // POST /api/v1/auth/login  body: { login, password }
      const response = await api.post('/auth/login', { login, password })
      onLoggedIn(response.data.token)
    } catch (err) {
      setError(formError(err))
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout>
      <h1>Welcome back</h1>
      <p className="sub">
        New to Quasario?{' '}
        <button type="button" className="link" onClick={goToRegister}>
          Create an account
        </button>
      </p>

      <form onSubmit={handleSubmit} noValidate>
        <Field
          label="Email or username"
          value={login}
          onChange={(e) => setLogin(e.target.value)}
          autoComplete="username"
        />
        <Field
          label="Password"
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          autoComplete="current-password"
        />

        {error && <div className="alert">{error}</div>}

        <button className="primary" type="submit" disabled={loading}>
          {loading ? 'Logging in…' : 'Log in'}
        </button>
        <p className="note">Admins log in here too.</p>
      </form>
    </AuthLayout>
  )
}
