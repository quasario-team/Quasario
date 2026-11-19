import { useState } from "react";
import { NavLink, useNavigate } from "react-router-dom";
import { readApiError } from "../api/client";
import { useAuth } from "../auth/useAuth";
import { messageFor } from "../messages";

// Settings menu (Figma NEW 46). Phase 1 rows: account, password, log out.
// "Delete account" is added in step 15.
export function SettingsPage() {
  const { signOut } = useAuth();
  const navigate = useNavigate();
  const [error, setError] = useState("");
  const [leaving, setLeaving] = useState(false);

  // Use case U21 "Log out".
  async function handleLogOut() {
    setError("");
    setLeaving(true);
    try {
      await signOut();
      navigate("/sign-in", {
        replace: true,
        state: { notice: "You have signed out." },
      });
    } catch (apiError) {
      // Line 2a: the server cannot be reached: stay signed in and show why.
      setError(messageFor(readApiError(apiError).code));
      setLeaving(false);
    }
  }

  return (
    <div className="app-page">
      <header className="app-header">
        <button
          className="back-button"
          type="button"
          onClick={() => navigate("/")}
          aria-label="Back"
        >
          ←
        </button>
        <h1>Settings</h1>
        <span className="back-button" aria-hidden="true" />
      </header>

      {error && <p className="alert alert-error">{error}</p>}

      <nav className="menu">
        <NavLink className="menu-item" to="/settings/account">
          Account <span aria-hidden="true">›</span>
        </NavLink>
        <NavLink className="menu-item" to="/settings/password">
          Change password <span aria-hidden="true">›</span>
        </NavLink>
      </nav>

      <div className="menu-gap" />

      <div className="menu">
        <button
          className="menu-item"
          type="button"
          onClick={handleLogOut}
          disabled={leaving}
        >
          {leaving ? "Logging out..." : "Log out"}
        </button>
      </div>
    </div>
  );
}
