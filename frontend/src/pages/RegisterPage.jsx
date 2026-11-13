import { useState } from 'react'
import api from '../api'
import { fieldErrors, formError } from '../errors'
import AuthLayout from '../components/AuthLayout'
import Field from '../components/Field'

const ACCOUNT_FIELDS = ['email', 'username', 'phone', 'password']

export default function RegisterPage({ onLoggedIn, goToLogin }) {
  // Step 1 = account data, step 2 = first profile.
  const [step, setStep] = useState(1)
  const [account, setAccount] = useState({ email: '', username: '', phone: '', password: '' })
  const [profile, setProfile] = useState({ job_category_id: '', job_title_id: '', city_id: '', experience: '' })
  const [lookups, setLookups] = useState({ cities: [], job_categories: [] })
  const [errors, setErrors] = useState({})
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  function changeAccount(event) {
    setAccount({ ...account, [event.target.name]: event.target.value })
  }

  function changeProfile(event) {
    const { name, value } = event.target
    // A new category means the old job title is no longer valid.
    if (name === 'job_category_id') {
      setProfile({ ...profile, job_category_id: value, job_title_id: '' })
    } else {
      setProfile({ ...profile, [name]: value })
    }
  }

  function showError(err) {
    setErrors(fieldErrors(err))
    setError(formError(err))
  }

  // Step 1: Laravel only checks the data, it saves nothing.
  async function submitAccount(event) {
    event.preventDefault()
    setErrors({})
    setError('')
    setLoading(true)
    try {
      await api.post('/auth/register/check', account)
      const response = await api.get('/lookups/registration')
      setLookups(response.data)
      setStep(2)
    } catch (err) {
      showError(err)
    } finally {
      setLoading(false)
    }
  }

  // Step 2: Laravel creates the account and the first profile, and gives a token.
  async function submitProfile(event) {
    event.preventDefault()
    setErrors({})
    setError('')
    setLoading(true)
    try {
      const response = await api.post('/auth/register', {
        ...account,
        job_category_id: Number(profile.job_category_id),
        job_title_id: Number(profile.job_title_id),
        city_id: Number(profile.city_id),
        experience: profile.experience || null,
      })
      onLoggedIn(response.data.token)
    } catch (err) {
      showError(err)
      // If the problem is in the account data (e.g. email taken), go back to step 1.
      const fields = Object.keys(err.response?.data?.errors ?? {})
      if (fields.some((field) => ACCOUNT_FIELDS.includes(field))) {
        setStep(1)
      }
    } finally {
      setLoading(false)
    }
  }

  const category = lookups.job_categories.find((c) => String(c.id) === profile.job_category_id)
  const titles = category ? category.job_titles : []

  return (
    <AuthLayout>
      <h1>Create your account</h1>
      <p className="sub">
        Already have one?{' '}
        <button type="button" className="link" onClick={goToLogin}>
          Log in
        </button>
      </p>
      <p className="steps">Step {step} of 2</p>

      {step === 1 && (
        <form onSubmit={submitAccount} noValidate>
          <Field label="Email" name="email" type="email" value={account.email} onChange={changeAccount} error={errors.email} />
          <Field label="Username" name="username" value={account.username} onChange={changeAccount} error={errors.username} />
          <Field label="Phone number" name="phone" value={account.phone} onChange={changeAccount} error={errors.phone} placeholder="+963 9xx xxx xxx" />
          <Field
            label="Password"
            name="password"
            type="password"
            value={account.password}
            onChange={changeAccount}
            error={errors.password}
            hint="At least 8 characters, with a letter and a number."
          />

          {error && <div className="alert">{error}</div>}

          <button className="primary" type="submit" disabled={loading}>
            {loading ? 'Checking…' : 'Continue'}
          </button>
        </form>
      )}

      {step === 2 && (
        <form onSubmit={submitProfile} noValidate>
          <label className="field">
            <span className="field-label">Job category</span>
            <select className="input" name="job_category_id" value={profile.job_category_id} onChange={changeProfile}>
              <option value="">Choose…</option>
              {lookups.job_categories.map((c) => (
                <option key={c.id} value={c.id}>{c.name}</option>
              ))}
            </select>
            {errors.job_category_id && <span className="field-error">{errors.job_category_id}</span>}
          </label>

          <label className="field">
            <span className="field-label">Job title</span>
            <select className="input" name="job_title_id" value={profile.job_title_id} onChange={changeProfile} disabled={!category}>
              <option value="">Choose…</option>
              {titles.map((t) => (
                <option key={t.id} value={t.id}>{t.name}</option>
              ))}
            </select>
            {errors.job_title_id && <span className="field-error">{errors.job_title_id}</span>}
          </label>

          <label className="field">
            <span className="field-label">City</span>
            <select className="input" name="city_id" value={profile.city_id} onChange={changeProfile}>
              <option value="">Choose…</option>
              {lookups.cities.map((c) => (
                <option key={c.id} value={c.id}>{c.name}</option>
              ))}
            </select>
            {errors.city_id && <span className="field-error">{errors.city_id}</span>}
          </label>

          <label className="field">
            <span className="field-label">Experience (optional)</span>
            <textarea className="input" name="experience" rows="3" value={profile.experience} onChange={changeProfile} />
          </label>

          {error && <div className="alert">{error}</div>}

          <div className="row">
            <button type="button" className="secondary" onClick={() => setStep(1)}>Back</button>
            <button className="primary" type="submit" disabled={loading}>
              {loading ? 'Creating…' : 'Create account'}
            </button>
          </div>
        </form>
      )}
    </AuthLayout>
  )
}
