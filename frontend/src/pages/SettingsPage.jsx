import { useState } from "react";
import { NavLink, useNavigate } from "react-router-dom";
import { readApiError } from "../api/client";
import { useAuth } from "../auth/useAuth";
import { DeleteAccountDialog } from "../components/DeleteAccountDialog";
import { messageFor } from "../messages";

// Settings menu (Figma NEW 46). Rows: my profiles (phase 2), account,
// password, log out and delete account.
export function SettingsPage() {
  const { user, signOut, forgetSession } = useAuth();
  const navigate = useNavigate();
  const [error, setError] = useState("");
  const [leaving, setLeaving] = useState(false);
  const [askDelete, setAskDelete] = useState(false);

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

  // Use case U23 "Delete account": Laravel already deleted the account
  // and its tokens, so we only forget the token in the browser.
  function handleDeleted() {
    forgetSession();
    navigate("/sign-in", {
      replace: true,
      state: { notice: "Your account was deleted." },
    });
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
        {/* An admin has no profiles. */}
        {user.role !== "admin" && (
          <NavLink className="menu-item" to="/profiles">
            My profiles <span aria-hidden="true">›</span>
          </NavLink>
        )}
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
        {/* An admin account cannot be deleted from here (backend: adminNotAllowed). */}
        {user.role !== "admin" && (
          <button
            className="menu-item danger"
            type="button"
            onClick={() => setAskDelete(true)}
          >
            Delete account
          </button>
        )}
      </div>

      {askDelete && (
        <DeleteAccountDialog
          onCancel={() => setAskDelete(false)}
          onDeleted={handleDeleted}
        />
      )}
    </div>
  );
}
