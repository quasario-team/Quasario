import { NavLink } from "react-router-dom";

// Any address that is not in App.jsx shows this page.
export function NotFoundPage() {
  return (
    <div className="app-page center-page">
      <h1>Page not found</h1>
      <p className="muted-text">The page you are looking for does not exist.</p>
      <NavLink className="btn btn-primary" to="/">
        Back to home
      </NavLink>
    </div>
  );
}
