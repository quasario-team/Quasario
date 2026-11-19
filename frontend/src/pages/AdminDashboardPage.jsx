import { NavLink } from "react-router-dom";
import { useAuth } from "../auth/useAuth";

// Temporary admin page (phase 1). Use case A01: after sign in the admin
// comes here. The real tools (users, reports, verification, lookups)
// come in the admin phase.
export function AdminDashboardPage() {
  const { user } = useAuth();

  return (
    <div className="app-page">
      <header className="app-header">
        <h1>Admin dashboard</h1>
        <NavLink className="icon-link" to="/settings" aria-label="Settings">
          ⚙
        </NavLink>
      </header>

      <section className="panel">
        <p className="muted-text">Signed in as</p>
        <h2>{user.username}</h2>
        <span className="chip">Admin</span>
      </section>

      <section className="panel">
        <h2>Coming in the next phases</h2>
        <ul className="muted-text">
          <li>Users: list, ban and unban</li>
          <li>Verification requests</li>
          <li>Reports</li>
          <li>Cities, job fields and job titles</li>
        </ul>
      </section>
    </div>
  );
}
