import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { updateAccount } from "../api/auth";
import { readApiError } from "../api/client";
import { useAuth } from "../auth/useAuth";
import { TextInput } from "../components/TextInput";
import { messageFor } from "../messages";

const FIELDS = ["email", "username", "phone"];

// Use case U23 "Manage account": change email, username and phone.
// Only the fields that really changed are sent (PATCH /me/account).
export function AccountSettingsPage() {
  const { user, updateUser } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({
    email: user.email,
    username: user.username,
    phone: user.phone ?? "",
  });
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

    // Empty fields are not allowed, and we keep only what changed.
    const errors = {};
    const changes = {};
    for (const name of FIELDS) {
      const value = form[name].trim();
      if (value === "") errors[name] = "requiredField";
      else if (value !== (user[name] ?? "")) changes[name] = value;
    }
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    if (Object.keys(changes).length === 0) {
      setNotice("Nothing changed.");
      return;
    }

    setSending(true);
    try {
      const savedUser = await updateAccount(changes);
      updateUser(savedUser); // the whole site now shows the new values
      setNotice("Your account was saved.");
    } catch (error) {
      // alreadyUsed (email / username of another account), invalidField, server off ...
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
        <h1>Account</h1>
        <span className="back-button" aria-hidden="true" />
      </header>

      <form className="panel form" onSubmit={handleSubmit} noValidate>
        {formError && <p className="alert alert-error">{formError}</p>}
        {notice && <p className="alert alert-success">{notice}</p>}

        <TextInput
          id="email"
          type="email"
          label="Email address"
          autoComplete="email"
          value={form.email}
          onChange={(value) => setField("email", value)}
          error={fieldErrors.email}
        />
        <TextInput
          id="username"
          label="User name"
          autoComplete="username"
          value={form.username}
          onChange={(value) => setField("username", value)}
          error={fieldErrors.username}
        />
        <TextInput
          id="phone"
          type="tel"
          label="Contact Number"
          autoComplete="tel"
          value={form.phone}
          onChange={(value) => setField("phone", value)}
          error={fieldErrors.phone}
        />

        <button
          className="btn btn-primary btn-block"
          type="submit"
          disabled={sending}
        >
          {sending ? "Saving..." : "Save"}
        </button>
      </form>
    </div>
  );
}
