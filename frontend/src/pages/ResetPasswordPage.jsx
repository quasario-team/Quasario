import { useState } from "react";
import { NavLink, useNavigate, useSearchParams } from "react-router-dom";
import { resetPassword } from "../api/auth";
import { readApiError } from "../api/client";
import { useAuth } from "../auth/useAuth";
import { AuthLayout } from "../components/AuthLayout";
import { TextInput } from "../components/TextInput";
import { messageFor } from "../messages";

const PASSWORD_HINT = "At least 8 characters, with letters and numbers.";

// Opened from the email link: /reset-password?token=...&email=...
// The user chooses a new password. Laravel then signs the account out
// everywhere (all old tokens are deleted), so the user signs in again.
export function ResetPasswordPage() {
  const [searchParams] = useSearchParams();
  const token = searchParams.get("token") ?? "";
  const email = searchParams.get("email") ?? "";

  const { forgetSession } = useAuth();
  const navigate = useNavigate();
  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [sending, setSending] = useState(false);

  // A link without token or email cannot work.
  if (token === "" || email === "") {
    return (
      <AuthLayout title="Reset password">
        <p className="alert alert-error">{messageFor("invalidToken")}</p>
        <div className="dialog-actions">
          <NavLink className="btn btn-outline" to="/sign-in">
            Sign in
          </NavLink>
          <NavLink className="btn btn-primary" to="/forgot-password">
            New link
          </NavLink>
        </div>
      </AuthLayout>
    );
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");

    const errors = {};
    if (password === "") errors.password = "requiredField";
    if (confirm === "") errors.confirm = "requiredField";
    else if (confirm !== password) errors.confirm = "passwordsDoNotMatch";
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    setSending(true);
    try {
      await resetPassword({ email, token, password });
      forgetSession(); // the old token (if any) does not work any more
      navigate("/sign-in", {
        replace: true,
        state: { notice: "Your password was changed. Please sign in." },
      });
    } catch (error) {
      // invalidToken (wrong or older than 60 minutes), weakPassword, server off ...
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
      setSending(false);
    }
  }

  return (
    <AuthLayout title="Reset password">
      <form className="form" onSubmit={handleSubmit} noValidate>
        <p className="auth-text">
          Choose a new password for <strong>{email}</strong>.
        </p>
        {formError && <p className="alert alert-error">{formError}</p>}

        <TextInput
          id="password"
          type="password"
          label="New password"
          placeholder="New password"
          autoComplete="new-password"
          hint={PASSWORD_HINT}
          value={password}
          onChange={(value) => {
            setPassword(value);
            setFieldErrors((current) => ({ ...current, password: undefined }));
          }}
          error={fieldErrors.password}
        />

        <TextInput
          id="confirm"
          type="password"
          label="Confirm new password"
          placeholder="Write it again"
          autoComplete="new-password"
          value={confirm}
          onChange={(value) => {
            setConfirm(value);
            setFieldErrors((current) => ({ ...current, confirm: undefined }));
          }}
          error={fieldErrors.confirm}
        />

        <button
          className="btn btn-primary btn-block"
          type="submit"
          disabled={sending}
        >
          {sending ? "Saving..." : "Save new password"}
        </button>
      </form>
    </AuthLayout>
  );
}
