import { useState } from "react";
import { NavLink, useLocation, useNavigate } from "react-router-dom";
import { readApiError } from "../api/client";
import { homePathFor } from "../auth/homePath";
import { useAuth } from "../auth/useAuth";
import { AuthLayout } from "../components/AuthLayout";
import { TextInput } from "../components/TextInput";
import { messageFor } from "../messages";

// Use cases U22 (user) and A01 (admin): same screen, then we go by role.
export function SignInPage() {
  const { signIn, endedReason } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const [loginValue, setLoginValue] = useState("");
  const [password, setPassword] = useState("");
  const [fieldErrors, setFieldErrors] = useState({});
  // A message for the whole form, e.g. "Wrong username / email or password."
  const [formError, setFormError] = useState(
    endedReason ? messageFor(endedReason) : "",
  );
  const [sending, setSending] = useState(false);

  // A green message sent by another page, e.g. after sign up or a password reset.
  const notice = location.state?.notice;

  async function handleSubmit(event) {
    event.preventDefault(); // stop the browser from reloading the page
    setFormError("");

    // Narrative 6a: an empty field shows an error before asking the server.
    const errors = {};
    if (loginValue.trim() === "") errors.login = "requiredField";
    if (password === "") errors.password = "requiredField";
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    setSending(true);
    try {
      const user = await signIn(loginValue.trim(), password);
      navigate(homePathFor(user), { replace: true });
    } catch (error) {
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      // 6b wrong data, 6c banned account, too many tries, server off ...
      setFormError(messageFor(code));
      setSending(false);
    }
  }

  return (
    <AuthLayout
      title="Sign in"
      switchText="No Account ?"
      switchLabel="Sign up"
      switchTo="/sign-up"
    >
      <form className="form" onSubmit={handleSubmit} noValidate>
        {notice && <p className="alert alert-success">{notice}</p>}
        {formError && <p className="alert alert-error">{formError}</p>}

        <TextInput
          id="login"
          label="Enter your username or email address"
          placeholder="Username or email address"
          autoComplete="username"
          value={loginValue}
          onChange={setLoginValue}
          error={fieldErrors.login}
        />

        <TextInput
          id="password"
          type="password"
          label="Enter your Password"
          placeholder="Password"
          autoComplete="current-password"
          value={password}
          onChange={setPassword}
          error={fieldErrors.password}
        />

        <div className="text-end">
          <NavLink className="link-button" to="/forgot-password">
            Forgot Password?
          </NavLink>
        </div>

        <button
          className="btn btn-primary btn-block"
          type="submit"
          disabled={sending}
        >
          {sending ? "Signing in..." : "Sign in"}
        </button>
      </form>
    </AuthLayout>
  );
}
