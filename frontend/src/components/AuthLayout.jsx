// The frame of the login and sign-up screens: green panel on the left, form on the right.
export default function AuthLayout({ children }) {
  return (
    <div className="auth">
      <aside className="auth-panel">
        <div className="brand">
          <span className="brand-logo">Q</span> Quasario
        </div>
        <h2>Trusted local pros, real work, verified reviews.</h2>
        <p className="panel-foot">Free for everyone in Syria.</p>
      </aside>

      <main className="auth-main">
        <div className="auth-box">{children}</div>
      </main>
    </div>
  )
}
