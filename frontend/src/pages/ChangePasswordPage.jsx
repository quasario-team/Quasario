import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { changePassword } from "../api/auth";
import { readApiError } from "../api/client";
import { TextInput } from "../components/TextInput";
import { messageFor } from "../messages";

const PASSWORD_HINT = "At least 8 characters, with letters and numbers.";
const EMPTY_FORM = { current_password: "", password: "", confirm: "" };

// Use case U23 "Manage account": change the password (PATCH /me/password).
// Laravel keeps this device signed in and signs out all the other devices.
export function ChangePasswordPage() {
  const navigate = useNavigate();
  const [form, setForm] = useState(EMPTY_FORM);
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [notice, setNotice] = useState("");
  const [sending, setSending] = useState(false);

  function setField(name, value) {
    setForm((current) => ({ ...current, [name]: value }));
    setFieldErrors((current) => ({ ...current, [name]: undefined }));
    setNotice("");
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");
    setNotice("");

    const errors = {};
    for (const name of ["current_password", "password", "confirm"]) {
      if (form[name] === "") errors[name] = "requiredField";
    }
    if (!errors.confirm && form.confirm !== form.password)
      errors.confirm = "passwordsDoNotMatch";
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    setSending(true);
    try {
      await changePassword(form.current_password, form.password);
      setForm(EMPTY_FORM); // empty the boxes: no password stays on the screen
      setNotice(
        "Your password was changed. Your other devices were signed out.",
      );
    } catch (error) {
      // wrongPassword (current password), weakPassword (new one), server off ...
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
    } finally {
      setSending(false);
    }
  }

  return (
    <div className="app-page">
      <header className="app-header">
        <button
          className="back-button"
          type="button"
          onClick={() => navigate("/settings")}
          aria-label="Back"
        >
          ←
        </button>
        <h1>Change password</h1>
        <span className="back-button" aria-hidden="true" />
      </header>

      <form className="panel form" onSubmit={handleSubmit} noValidate>
        {formError && <p className="alert alert-error">{formError}</p>}
        {notice && <p className="alert alert-success">{notice}</p>}

        <TextInput
          id="current_password"
          type="password"
          label="Current password"
          autoComplete="current-password"
          value={form.current_password}
          onChange={(value) => setField("current_password", value)}
          error={fieldErrors.current_password}
        />
        <TextInput
          id="password"
          type="password"
          label="New password"
          autoComplete="new-password"
          hint={PASSWORD_HINT}
          value={form.password}
          onChange={(value) => setField("password", value)}
          error={fieldErrors.password}
        />
        <TextInput
          id="confirm"
          type="password"
          label="Confirm new password"
          autoComplete="new-password"
          value={form.confirm}
          onChange={(value) => setField("confirm", value)}
          error={fieldErrors.confirm}
        />

        <button
          className="btn btn-primary btn-block"
          type="submit"
          disabled={sending}
        >
          {sending ? "Saving..." : "Change password"}
        </button>
      </form>
    </div>
  );
}
