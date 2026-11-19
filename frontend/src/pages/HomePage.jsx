import { Navigate, NavLink } from "react-router-dom";
import { LoadingScreen } from "../components/LoadingScreen";
import { useAuth } from "../auth/useAuth";

// Temporary home page (phase 1). The real home (search, categories ...)
// comes in a later phase. Narrative U22: after sign in the user sees
// the name of the active profile.
export function HomePage() {
  const { status, user, activeProfile } = useAuth();

  if (status === "loading") return <LoadingScreen />;

  // The admin has no profiles: his home is the dashboard.
  if (user?.role === "admin") return <Navigate to="/admin" replace />;

  if (status === "guest") {
    return (
      <div className="app-page center-page">
        <h1>
          Welcome to <span className="brand-dark">Quasario</span>
        </h1>
        <p className="muted-text">Find local services near you.</p>
        <div className="home-actions">
          <NavLink className="btn btn-primary" to="/sign-in">
            Sign in
          </NavLink>
          <NavLink className="btn btn-outline" to="/sign-up">
            Sign up
          </NavLink>
        </div>
      </div>
    );
  }

  return (
    <div className="app-page">
      <header className="app-header">
        <h1>Quasario</h1>
        <NavLink className="icon-link" to="/settings" aria-label="Settings">
          ⚙
        </NavLink>
      </header>

      <section className="panel">
        <p className="muted-text">Hello,</p>
        <h2>{activeProfile ? activeProfile.display_name : user.username}</h2>
        {activeProfile ? (
          <span className="chip">
            {activeProfile.job_title?.name} · {activeProfile.city?.name}
          </span>
        ) : (
          <p className="muted-text">You have no profile yet.</p>
        )}
      </section>

      <p className="muted-text home-note">
        More pages come in the next phases.
      </p>
    </div>
  );
}
