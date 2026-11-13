import { useEffect, useState } from 'react'
import api from '../api'
import { formError } from '../errors'

// A simple page after login, to prove that the token works.
export default function HomePage({ onLoggedOut }) {
  const [me, setMe] = useState(null)
  const [error, setError] = useState('')

  useEffect(() => {
    api
      .get('/me')
      .then((response) => setMe(response.data))
      .catch((err) => {
        // 401 = the token is not valid any more: go back to login.
        if (err.response?.status === 401) {
          onLoggedOut()
        } else {
          setError(formError(err))
        }
      })
  }, [onLoggedOut])

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch {
      // Even if the server fails, we remove the token from this browser.
    }
    onLoggedOut()
  }

  return (
    <div className="home">
      <div className="auth-box">
        <h1>Quasario</h1>
        {error && <div className="alert">{error}</div>}
        {!me && !error && <p>Loading…</p>}
        {me && (
          <>
            <p>
              Logged in as <b>{me.user.username}</b> ({me.user.role})
            </p>
            {me.active_profile && (
              <p>
                Active profile: {me.active_profile.display_name} · {me.active_profile.job_title.name} ·{' '}
                {me.active_profile.city.name}
              </p>
            )}
          </>
        )}
        <button className="secondary" type="button" onClick={logout}>
          Log out
        </button>
      </div>
    </div>
  )
}
