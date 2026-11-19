import { useState } from "react";
import { NavLink } from "react-router-dom";
import { forgotPassword } from "../api/auth";
import { readApiError } from "../api/client";
import { AuthLayout } from "../components/AuthLayout";
import { TextInput } from "../components/TextInput";
import { messageFor } from "../messages";

// "Forgot password": the user writes the email, Laravel sends a link.
// Laravel answers the same way for every email (known or not), so nobody
// can use this page to find out which emails have an account.
export function ForgotPasswordPage() {
  const [email, setEmail] = useState("");
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [sending, setSending] = useState(false);
  const [sent, setSent] = useState(false);

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");

    if (email.trim() === "") {
      setFieldErrors({ email: "requiredField" });
      return;
    }
    setFieldErrors({});

    setSending(true);
    try {
      await forgotPassword(email.trim());
      setSent(true); // show "Check your email"
    } catch (error) {
      // wrong email format, too many tries (3 a minute), server off ...
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
    } finally {
      setSending(false);
    }
  }

  if (sent) {
    return (
      <AuthLayout title="Check your email">
        <p className="auth-text">
          If an account uses <strong>{email.trim()}</strong>, we sent it a link
          to choose a new password. The link works for 60 minutes.
        </p>
        <div className="dialog-actions">
          <button
            className="btn btn-outline"
            type="button"
            onClick={() => setSent(false)}
          >
            Send again
          </button>
          <NavLink className="btn btn-primary" to="/sign-in">
            Sign in
          </NavLink>
        </div>
      </AuthLayout>
    );
  }

  return (
    <AuthLayout
      title="Forgot password"
      switchText="Remember it ?"
      switchLabel="Sign in"
      switchTo="/sign-in"
    >
      <form className="form" onSubmit={handleSubmit} noValidate>
        <p className="auth-text">
          Write the email of your account. We will send you a link to choose a
          new password.
        </p>
        {formError && <p className="alert alert-error">{formError}</p>}

        <TextInput
          id="email"
          type="email"
          label="Enter your email address"
          placeholder="Email address"
          autoComplete="email"
          value={email}
          onChange={(value) => {
            setEmail(value);
            setFieldErrors({});
          }}
          error={fieldErrors.email}
        />

        <button
          className="btn btn-primary btn-block"
          type="submit"
          disabled={sending}
        >
          {sending ? "Sending..." : "Send link"}
        </button>
      </form>
    </AuthLayout>
  );
}
