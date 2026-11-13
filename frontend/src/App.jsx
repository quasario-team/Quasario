import { useCallback, useState } from 'react'
import HomePage from './pages/HomePage'
import LoginPage from './pages/LoginPage'
import RegisterPage from './pages/RegisterPage'

export default function App() {
  // Which screen is shown: 'login', 'register' or 'home'.
  const [page, setPage] = useState(() => (localStorage.getItem('token') ? 'home' : 'login'))

  function handleLoggedIn(token) {
    localStorage.setItem('token', token)
    setPage('home')
  }

  const handleLoggedOut = useCallback(() => {
    localStorage.removeItem('token')
    setPage('login')
  }, [])

  if (page === 'home') {
    return <HomePage onLoggedOut={handleLoggedOut} />
  }
  if (page === 'register') {
    return <RegisterPage onLoggedIn={handleLoggedIn} goToLogin={() => setPage('login')} />
  }
  return <LoginPage onLoggedIn={handleLoggedIn} goToRegister={() => setPage('register')} />
}
