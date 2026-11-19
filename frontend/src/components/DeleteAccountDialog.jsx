import { useState } from "react";
import { deleteAccount } from "../api/auth";
import { readApiError } from "../api/client";
import { messageFor } from "../messages";
import { TextInput } from "./TextInput";

// Confirm window for "Delete account" (Figma NEW 47).
// We also ask for the password, so someone who finds an open session
// cannot delete the account alone (DELETE /me checks it).
export function DeleteAccountDialog({ onCancel, onDeleted }) {
  const [password, setPassword] = useState("");
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [sending, setSending] = useState(false);

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");

    if (password === "") {
      setFieldErrors({ password: "requiredField" });
      return;
    }

    setSending(true);
    try {
      await deleteAccount(password);
      onDeleted();
    } catch (error) {
      // wrongPassword, adminNotAllowed, server off ...
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
      setSending(false);
    }
  }

  return (
    <div className="dialog-backdrop" role="presentation">
      <form
        className="dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-title"
        onSubmit={handleSubmit}
        noValidate
      >
        <h2 id="delete-title">Delete your account?</h2>
        <p>
          All your profiles will be deleted too. This cannot be undone. Write
          your password to confirm.
        </p>
        {formError && <p className="alert alert-error">{formError}</p>}

        <TextInput
          id="delete_password"
          type="password"
          label="Password"
          autoComplete="current-password"
          autoFocus
          value={password}
          onChange={(value) => {
            setPassword(value);
            setFieldErrors({});
          }}
          error={fieldErrors.password}
        />

        <div className="dialog-actions">
          <button
            className="btn btn-outline"
            type="button"
            onClick={onCancel}
            disabled={sending}
          >
            Cancel
          </button>
          <button className="btn btn-danger" type="submit" disabled={sending}>
            {sending ? "Deleting..." : "Delete"}
          </button>
        </div>
      </form>
    </div>
  );
}
