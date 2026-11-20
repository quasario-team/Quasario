import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { readApiError } from "../api/client";
import { updateProfile } from "../api/profiles";
import { useAuth } from "../auth/useAuth";
import { ProfileImagesEditor } from "../components/ProfileImagesEditor";
import { TextArea } from "../components/TextArea";
import { TextInput } from "../components/TextInput";
import { messageFor } from "../messages";

const FIELDS = ["display_name", "bio", "study"];

// "Complete your profile" (phase 2, feature 5, Onboarding).
// Sign up gave the first profile only the username as its name.
// Here the user adds a display name, pictures, a bio and a study.
// Everything is optional except the name, and "Skip for now" is always there.
export function OnboardingPage() {
  const navigate = useNavigate();
  const { activeProfile, refreshMe } = useAuth();
  const [form, setForm] = useState(() => ({
    display_name: activeProfile?.display_name ?? "",
    bio: activeProfile?.bio ?? "",
    study: activeProfile?.study ?? "",
  }));
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [sending, setSending] = useState(false);

  // An account without an active profile (should not happen) goes home.
  if (!activeProfile) {
    return (
      <div className="app-page center-page">
        <p className="muted-text">You have no profile yet.</p>
        <button
          className="btn btn-primary"
          type="button"
          onClick={() => navigate("/")}
        >
          Home
        </button>
      </div>
    );
  }

  function setField(name, value) {
    setForm((current) => ({ ...current, [name]: value }));
    setFieldErrors((current) => ({ ...current, [name]: undefined }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");

    if (form.display_name.trim() === "") {
      setFieldErrors({ display_name: "requiredField" });
      return;
    }

    // Send only what changed; an empty text becomes null (no text).
    const changes = {};
    for (const name of FIELDS) {
      const text = form[name].trim();
      const saved = activeProfile[name] ?? "";
      if (text !== saved) changes[name] = text === "" ? null : text;
    }

    setSending(true);
    try {
      if (Object.keys(changes).length > 0) {
        await updateProfile(activeProfile.id, changes);
        await refreshMe();
      }
      navigate("/", { replace: true });
    } catch (error) {
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
      setSending(false);
    }
  }

  return (
    <div className="app-page">
      <header className="app-header">
        <span className="back-button" aria-hidden="true" />
        <h1>Complete your profile</h1>
        <span className="back-button" aria-hidden="true" />
      </header>

      <p className="muted-text home-note">
        People find you faster with a photo and a few words about your work.
      </p>
      <div className="menu-gap" />

      {/* The pictures are saved at once; the editor tells us, and we update the session. */}
      <ProfileImagesEditor
        profile={activeProfile}
        onUploaded={() => refreshMe()}
      />

      <form className="form panel" onSubmit={handleSubmit} noValidate>
        {formError && <p className="alert alert-error">{formError}</p>}

        <TextInput
          id="display_name"
          label="Display name"
          hint="The name people see on your profile."
          maxLength={100}
          value={form.display_name}
          onChange={(value) => setField("display_name", value)}
          error={fieldErrors.display_name}
        />

        <TextArea
          id="bio"
          label="Bio (optional)"
          maxLength={2000}
          value={form.bio}
          onChange={(value) => setField("bio", value)}
          error={fieldErrors.bio}
        />

        <TextArea
          id="study"
          label="Study (optional)"
          maxLength={2000}
          value={form.study}
          onChange={(value) => setField("study", value)}
          error={fieldErrors.study}
        />

        <div className="dialog-actions">
          <button
            className="btn btn-outline"
            type="button"
            onClick={() => navigate("/", { replace: true })}
            disabled={sending}
          >
            Skip for now
          </button>
          <button className="btn btn-primary" type="submit" disabled={sending}>
            {sending ? "Saving..." : "Save"}
          </button>
        </div>
      </form>
    </div>
  );
}
