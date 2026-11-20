import { useEffect, useState } from "react";
import { NavLink, useNavigate } from "react-router-dom";
import { readApiError } from "../api/client";
import { activateProfile, deleteProfile, getMyProfiles } from "../api/profiles";
import { useAuth } from "../auth/useAuth";
import { ConfirmDialog } from "../components/ConfirmDialog";
import { messageFor } from "../messages";

// The same limit as the backend (ProfileService): at most 5 profiles.
const MAX_PROFILES = 5;

// "My profiles" page (phase 2, features 4 and 5): list, add, edit, delete, switch.
export function MyProfilesPage() {
  const navigate = useNavigate();
  const { refreshMe } = useAuth();
  const [profiles, setProfiles] = useState(null); // null = still loading
  const [error, setError] = useState("");
  const [toDelete, setToDelete] = useState(null); // the profile in the confirm window
  const [deleting, setDeleting] = useState(false);
  const [switching, setSwitching] = useState(false);

  // When the page opens: ask Laravel for my profiles.
  useEffect(() => {
    getMyProfiles()
      .then(setProfiles)
      .catch((apiError) => {
        setError(messageFor(readApiError(apiError).code));
        setProfiles([]);
      });
  }, []);

  // Feature 5 "switch profile": this profile becomes the active one.
  async function handleActivate(profile) {
    setError("");
    setSwitching(true);
    try {
      await activateProfile(profile.id);
      // The list shows the active profile first: load it again.
      setProfiles(await getMyProfiles());
      // The home page shows the active profile: tell it too.
      await refreshMe();
    } catch (apiError) {
      setError(messageFor(readApiError(apiError).code));
    }
    setSwitching(false);
  }

  async function handleDelete() {
    setError("");
    setDeleting(true);
    try {
      await deleteProfile(toDelete.id);
      // Load the list again: if we deleted the active profile,
      // Laravel made another one active.
      setProfiles(await getMyProfiles());
      // Also tell the rest of the site (home page) which profile is active now.
      await refreshMe();
    } catch (apiError) {
      // lastProfile, notFound, server off ...
      setError(messageFor(readApiError(apiError).code));
    }
    setDeleting(false);
    setToDelete(null);
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
        <h1>My profiles</h1>
        <span className="back-button" aria-hidden="true" />
      </header>

      {error && <p className="alert alert-error">{error}</p>}

      {profiles === null ? (
        <p className="muted-text">Loading...</p>
      ) : (
        <>
          {profiles.map((profile) => (
            <section className="panel" key={profile.id}>
              <h2>
                {profile.display_name}{" "}
                {profile.is_active && (
                  <span className="brand-dark">· Active</span>
                )}
              </h2>
              <span className="chip">
                {profile.job_title.name} · {profile.city.name}
              </span>
              <div className="profile-card-actions">
                {!profile.is_active && (
                  <button
                    className="btn btn-primary"
                    type="button"
                    onClick={() => handleActivate(profile)}
                    disabled={switching}
                  >
                    {switching ? "Switching..." : "Use this profile"}
                  </button>
                )}
                <NavLink
                  className="btn btn-outline"
                  to={`/profiles/${profile.id}/edit`}
                >
                  Edit
                </NavLink>
                {/* The last profile cannot be deleted (backend: lastProfile). */}
                <button
                  className="btn btn-danger-outline"
                  type="button"
                  onClick={() => setToDelete(profile)}
                  disabled={profiles.length === 1}
                >
                  Delete
                </button>
              </div>
            </section>
          ))}

          <div className="menu-gap" />

          {profiles.length < MAX_PROFILES ? (
            <NavLink className="btn btn-primary btn-block" to="/profiles/new">
              Add profile
            </NavLink>
          ) : (
            <p className="muted-text">
              An account can have at most {MAX_PROFILES} profiles.
            </p>
          )}
          <p className="field-hint text-end">
            {profiles.length} / {MAX_PROFILES} profiles
          </p>
        </>
      )}

      {toDelete && (
        <ConfirmDialog
          title={`Delete "${toDelete.display_name}"?`}
          text="This profile and everything in it will be deleted. This cannot be undone."
          confirmLabel="Delete"
          busyLabel="Deleting..."
          busy={deleting}
          onCancel={() => setToDelete(null)}
          onConfirm={handleDelete}
        />
      )}
    </div>
  );
}
