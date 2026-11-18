import { NavLink } from "react-router-dom";

// The frame of the guest pages (Figma): sky background, grey card,
// "Welcome to Quasario" at the top left and a small link at the top right.
export function AuthLayout({
  title,
  switchText,
  switchLabel,
  switchTo,
  children,
}) {
  return (
    <div className="auth-page">
      <div className="auth-card">
        <div className="auth-top">
          <p>
            Welcome to <span className="brand">Quasario</span>
          </p>
          {switchTo && (
            <div className="switch">
              {switchText}
              <NavLink className="link-button" to={switchTo}>
                {switchLabel}
              </NavLink>
            </div>
          )}
        </div>
        <h1 className="auth-title">{title}</h1>
        {children}
      </div>
    </div>
  );
}
