import { useEffect, useState } from "react";
import { getRegistrationLookups, register } from "../../api/auth";
import { readApiError } from "../../api/client";
import { SelectInput } from "../../components/SelectInput";
import { TextArea } from "../../components/TextArea";
import { messageFor } from "../../messages";

const ACCOUNT_FIELDS = ["email", "username", "phone", "password"];

// Sign up, part 2 (U04 line 6, "Onboarding"): the first profile.
// "Finish" sends everything (account + profile) to POST /auth/register.
export function ProfileStep({
  account,
  profile,
  onChange,
  onBack,
  onAccountErrors,
  onDone,
}) {
  const [lookups, setLookups] = useState(null); // { cities, job_categories }
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [sending, setSending] = useState(false);

  // Load the cities, job fields and job titles once.
  useEffect(() => {
    getRegistrationLookups()
      .then(setLookups)
      .catch((error) => setFormError(messageFor(readApiError(error).code)));
  }, []);

  // The titles of the chosen job field only.
  const chosenCategory = lookups?.job_categories.find(
    (category) => String(category.id) === profile.job_category_id,
  );
  const titles = chosenCategory ? chosenCategory.job_titles : [];

  function setField(name, value) {
    const changes = { [name]: value };
    // A new job field means the old job title does not fit any more.
    if (name === "job_category_id") changes.job_title_id = "";
    onChange({ ...profile, ...changes });
    setFieldErrors((current) => ({ ...current, [name]: undefined }));
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");

    // Line 7a: the required fields must be chosen.
    const errors = {};
    for (const name of ["job_category_id", "job_title_id", "city_id"]) {
      if (profile[name] === "") errors[name] = "requiredField";
    }
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    setSending(true);
    try {
      await register({
        ...account,
        job_category_id: Number(profile.job_category_id),
        job_title_id: Number(profile.job_title_id),
        city_id: Number(profile.city_id),
        experience:
          profile.experience.trim() === "" ? null : profile.experience.trim(),
      });
      onDone(); // line 9: success message + Sign in page
    } catch (error) {
      const { code, fields } = readApiError(error);

      // Line 8a: someone took the email or username in the meantime: back to part 1.
      const accountErrors = {};
      for (const name of ACCOUNT_FIELDS) {
        if (fields[name]) accountErrors[name] = fields[name];
      }
      if (Object.keys(accountErrors).length > 0) {
        onAccountErrors(accountErrors);
        return;
      }

      // Line 8b: saving failed: stay here and show the message.
      setFieldErrors(fields);
      setFormError(messageFor(code));
      setSending(false);
    }
  }

  return (
    <form className="form" onSubmit={handleSubmit} noValidate>
      <p className="auth-text">
        Tell us about your work. You can add more profiles later (up to 5).
      </p>
      {formError && <p className="alert alert-error">{formError}</p>}

      <SelectInput
        id="job_category_id"
        label="Job field"
        placeholder={lookups ? "Choose a field" : "Loading..."}
        options={lookups?.job_categories ?? []}
        value={profile.job_category_id}
        onChange={(value) => setField("job_category_id", value)}
        error={fieldErrors.job_category_id}
      />

      <SelectInput
        id="job_title_id"
        label="Job title"
        placeholder={chosenCategory ? "Choose a title" : "Choose a field first"}
        options={titles}
        value={profile.job_title_id}
        onChange={(value) => setField("job_title_id", value)}
        error={fieldErrors.job_title_id}
        disabled={!chosenCategory}
      />

      <SelectInput
        id="city_id"
        label="City"
        placeholder={lookups ? "Choose a city" : "Loading..."}
        options={lookups?.cities ?? []}
        value={profile.city_id}
        onChange={(value) => setField("city_id", value)}
        error={fieldErrors.city_id}
      />

      <TextArea
        id="experience"
        label="Experience (optional)"
        placeholder="Text..."
        maxLength={2000}
        value={profile.experience}
        onChange={(value) => setField("experience", value)}
        error={fieldErrors.experience}
      />

      <div className="dialog-actions">
        <button
          className="btn btn-outline"
          type="button"
          onClick={onBack}
          disabled={sending}
        >
          Back
        </button>
        <button
          className="btn btn-primary"
          type="submit"
          disabled={sending || !lookups}
        >
          {sending ? "Saving..." : "Finish"}
        </button>
      </div>
    </form>
  );
}
