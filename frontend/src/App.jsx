import { useState } from "react";
import { readApiError } from "./api/client";
import { useAuth } from "./auth/useAuth";
import { messageFor } from "./messages";

// Temporary page: tests sign in / sign out with the demo account. We replace it in step 5.
export default function App() {
  const { status, user, signIn, signOut } = useAuth();
  const [error, setError] = useState("");

  async function testSignIn(password) {
    setError("");
    try {
      await signIn("demo_user", password);
    } catch (err) {
      setError(messageFor(readApiError(err).code));
    }
  }

  async function testSignOut() {
    setError("");
    try {
      await signOut();
    } catch (err) {
      setError(messageFor(readApiError(err).code));
    }
  }

  return (
    <div className="auth-page">
      <div className="auth-card">
        <h1 className="auth-title">Auth test</h1>
        <div className="form">
          <p>
            Status: <strong>{status}</strong>
          </p>
          {user && (
            <p>
              User: {user.username} ({user.role})
            </p>
          )}
          {error && <p className="alert alert-error">{error}</p>}

          {status === "guest" && (
            <>
              <button
                className="btn btn-primary btn-block"
                type="button"
                onClick={() => testSignIn("password1")}
              >
                Sign in as demo_user
              </button>
              <button
                className="btn btn-outline btn-block"
                type="button"
                onClick={() => testSignIn("wrong-pass1")}
              >
                Sign in with a wrong password
              </button>
            </>
          )}

          {status === "signedIn" && (
            <button
              className="btn btn-primary btn-block"
              type="button"
              onClick={testSignOut}
            >
              Sign out
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
