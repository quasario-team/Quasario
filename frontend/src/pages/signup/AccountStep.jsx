import { useState } from "react";
import { checkRegistration } from "../../api/auth";
import { readApiError } from "../../api/client";
import { TextInput } from "../../components/TextInput";
import { messageFor } from "../../messages";

const PASSWORD_HINT = "At least 8 characters, with letters and numbers.";

// Sign up, part 1 (U04 lines 3-5): account fields. The server checks them
// (POST /auth/register/check) but saves nothing yet.
// initialErrors: errors sent back by part 2 (line 8a), shown when we come back.
export function AccountStep({ account, onChange, onDone, initialErrors = {} }) {
  const [fieldErrors, setFieldErrors] = useState(initialErrors);
  const [formError, setFormError] = useState(
    Object.keys(initialErrors).length > 0 ? messageFor("validationFailed") : "",
  );
  const [sending, setSending] = useState(false);

  // Change one field and remove its old error.
  function setField(name, value) {
    onChange({ ...account, [name]: value });
    setFieldErrors((current) => ({ ...current, [name]: undefined }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");

    // Line 4a: empty fields are caught here, before asking the server.
    const errors = {};
    for (const name of ["email", "username", "phone", "password"]) {
      if (account[name].trim() === "") errors[name] = "requiredField";
    }
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    setSending(true);
    try {
      await checkRegistration(account);
      onDone(); // all good: show part 2 (the first profile)
    } catch (error) {
      // 4a wrong format, 4b email / username already used, 4c weak password
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
    } finally {
      setSending(false);
    }
  }

  return (
    <form className="form" onSubmit={handleSubmit} noValidate>
      {formError && <p className="alert alert-error">{formError}</p>}

      <TextInput
        id="email"
        type="email"
        label="Enter your email address"
        placeholder="Email address"
        autoComplete="email"
        value={account.email}
        onChange={(value) => setField("email", value)}
        error={fieldErrors.email}
      />

      <div className="field-row">
        <TextInput
          id="username"
          label="User name"
          placeholder="User name"
          autoComplete="username"
          value={account.username}
          onChange={(value) => setField("username", value)}
          error={fieldErrors.username}
        />
        <TextInput
          id="phone"
          type="tel"
          label="Contact Number"
          placeholder="+963..."
          autoComplete="tel"
          value={account.phone}
          onChange={(value) => setField("phone", value)}
          error={fieldErrors.phone}
        />
      </div>

      <TextInput
        id="password"
        type="password"
        label="Enter your Password"
        placeholder="Password"
        autoComplete="new-password"
        hint={PASSWORD_HINT}
        value={account.password}
        onChange={(value) => setField("password", value)}
        error={fieldErrors.password}
      />

      <button
        className="btn btn-primary btn-block"
        type="submit"
        disabled={sending}
      >
        {sending ? "Checking..." : "Next"}
      </button>
    </form>
  );
}
