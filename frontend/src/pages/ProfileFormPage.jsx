import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { getRegistrationLookups } from "../api/auth";
import { readApiError } from "../api/client";
import { createProfile, getMyProfiles, updateProfile } from "../api/profiles";
import { useAuth } from "../auth/useAuth";
import { GalleryEditor } from "../components/GalleryEditor";
import { LocationEditor } from "../components/LocationEditor";
import { ProfileImagesEditor } from "../components/ProfileImagesEditor";
import { SelectInput } from "../components/SelectInput";
import { SocialLinksEditor } from "../components/SocialLinksEditor";
import { TextArea } from "../components/TextArea";
import { TextInput } from "../components/TextInput";
import { messageFor } from "../messages";

const EMPTY_FORM = {
  display_name: "",
  job_category_id: "",
  job_title_id: "",
  city_id: "",
  bio: "",
  study: "",
  experience: "",
};
const REQUIRED = ["display_name", "job_category_id", "job_title_id", "city_id"];
const TEXTS = ["bio", "study", "experience"];

// Turn a profile from Laravel into the values of the form (all strings).
function formFromProfile(profile) {
  return {
    display_name: profile.display_name,
    job_category_id: String(profile.job_title.job_category.id),
    job_title_id: String(profile.job_title.id),
    city_id: String(profile.city.id),
    bio: profile.bio ?? "",
    study: profile.study ?? "",
    experience: profile.experience ?? "",
  };
}

// Turn the form into what Laravel wants: numbers for the lists,
// trimmed text, and null for an empty optional text.
function dataFromForm(form) {
  const data = {
    display_name: form.display_name.trim(),
    job_category_id: Number(form.job_category_id),
    job_title_id: Number(form.job_title_id),
    city_id: Number(form.city_id),
  };
  for (const name of TEXTS) {
    const text = form[name].trim();
    data[name] = text === "" ? null : text;
  }
  return data;
}

// One page for two jobs (phase 2, features 4 to 8):
//   /profiles/new       -> add a profile   (POST  /me/profiles)
//   /profiles/:id/edit  -> edit a profile  (PATCH /me/profiles/{id})
export function ProfileFormPage() {
  const { id } = useParams(); // undefined on /profiles/new
  const isEdit = id !== undefined;
  const navigate = useNavigate();
  const { refreshMe } = useAuth();

  const [lookups, setLookups] = useState(null); // { cities, job_categories }
  const [form, setForm] = useState(EMPTY_FORM);
  const [original, setOriginal] = useState(null); // the saved values (edit only)
  const [profile, setProfile] = useState(null); // the profile from Laravel (edit only)
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [sending, setSending] = useState(false);

  // Load the lists; on edit also load the profile we are editing.
  useEffect(() => {
    async function load() {
      try {
        setLookups(await getRegistrationLookups());
        if (isEdit) {
          const profiles = await getMyProfiles();
          const found = profiles.find((item) => String(item.id) === id);
          if (!found) {
            setFormError(messageFor("notFound"));
            return;
          }
          const values = formFromProfile(found);
          setProfile(found);
          setForm(values);
          setOriginal(values);
        }
      } catch (error) {
        setFormError(messageFor(readApiError(error).code));
      }
    }
    load();
  }, [id, isEdit]);

  // The titles of the chosen job field only.
  const chosenCategory = lookups?.job_categories.find(
    (category) => String(category.id) === form.job_category_id,
  );
  const titles = chosenCategory ? chosenCategory.job_titles : [];

  function setField(name, value) {
    const changes = { [name]: value };
    // A new job field means the old job title does not fit any more.
    if (name === "job_category_id") changes.job_title_id = "";
    setForm((current) => ({ ...current, ...changes }));
    setFieldErrors((current) => ({ ...current, [name]: undefined }));
  }

  // A new picture was uploaded: show it, and update the home page too.
  async function handleImagesUploaded(updated) {
    setProfile(updated);
    await refreshMe();
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");

    // The required fields must be filled.
    const errors = {};
    for (const name of REQUIRED) {
      if (form[name].trim() === "") errors[name] = "requiredField";
    }
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    const data = dataFromForm(form);

    setSending(true);
    try {
      if (isEdit) {
        // Send only what changed. The job field and the job title go together,
        // because Laravel checks that the title belongs to the field.
        const changes = {};
        for (const name of Object.keys(EMPTY_FORM)) {
          if (form[name] !== original[name]) changes[name] = data[name];
        }
        if ("job_category_id" in changes || "job_title_id" in changes) {
          changes.job_category_id = data.job_category_id;
          changes.job_title_id = data.job_title_id;
        }
        if (Object.keys(changes).length > 0) {
          await updateProfile(id, changes);
        }
      } else {
        await createProfile(data);
      }
      // The home page shows the active profile: keep it up to date.
      await refreshMe();
      navigate("/profiles");
    } catch (error) {
      // requiredField, invalidField, profileLimitReached, notFound ...
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
      setSending(false);
    }
  }

  return (
    <div className="app-page">
      <header className="app-header">
        <button
          className="back-button"
          type="button"
          onClick={() => navigate("/profiles")}
          aria-label="Back"
        >
          ←
        </button>
        <h1>{isEdit ? "Edit profile" : "New profile"}</h1>
        <span className="back-button" aria-hidden="true" />
      </header>

      {/* Pictures can be added only to a saved profile (edit), not on "New profile". */}
      {profile && (
        <ProfileImagesEditor
          profile={profile}
          onUploaded={handleImagesUploaded}
        />
      )}

      <form className="form panel" onSubmit={handleSubmit} noValidate>
        {formError && <p className="alert alert-error">{formError}</p>}

        <TextInput
          id="display_name"
          label="Display name"
          maxLength={100}
          value={form.display_name}
          onChange={(value) => setField("display_name", value)}
          error={fieldErrors.display_name}
        />

        <SelectInput
          id="job_category_id"
          label="Job field"
          placeholder={lookups ? "Choose a field" : "Loading..."}
          options={lookups?.job_categories ?? []}
          value={form.job_category_id}
          onChange={(value) => setField("job_category_id", value)}
          error={fieldErrors.job_category_id}
        />

        <SelectInput
          id="job_title_id"
          label="Job title"
          placeholder={
            chosenCategory ? "Choose a title" : "Choose a field first"
          }
          options={titles}
          value={form.job_title_id}
          onChange={(value) => setField("job_title_id", value)}
          error={fieldErrors.job_title_id}
          disabled={!chosenCategory}
        />

        <SelectInput
          id="city_id"
          label="City"
          placeholder={lookups ? "Choose a city" : "Loading..."}
          options={lookups?.cities ?? []}
          value={form.city_id}
          onChange={(value) => setField("city_id", value)}
          error={fieldErrors.city_id}
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

        <TextArea
          id="experience"
          label="Experience (optional)"
          maxLength={2000}
          value={form.experience}
          onChange={(value) => setField("experience", value)}
          error={fieldErrors.experience}
        />

        <div className="dialog-actions">
          <button
            className="btn btn-outline"
            type="button"
            onClick={() => navigate("/profiles")}
            disabled={sending}
          >
            Cancel
          </button>
          <button
            className="btn btn-primary"
            type="submit"
            disabled={sending || !lookups || (isEdit && !original)}
          >
            {sending ? "Saving..." : "Save"}
          </button>
        </div>
      </form>

      {/* Contact links have their own Save button (PUT /me/profiles/{id}/links). */}
      {profile && <SocialLinksEditor profileId={profile.id} />}

      {/* The work gallery: each picture is added or deleted at once. */}
      {profile && <GalleryEditor profileId={profile.id} />}

      {/* The place of work: its own Save and Delete buttons. */}
      {profile && <LocationEditor profileId={profile.id} />}
    </div>
  );
}
