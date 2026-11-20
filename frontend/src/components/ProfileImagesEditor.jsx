import { createElement, useState } from "react";
import { readApiError } from "../api/client";
import { uploadProfileImages } from "../api/profiles";
import { messageFor } from "../messages";
import { ImagePickerButton } from "./ImagePickerButton";

// A photo tag (built with createElement, like the TextInput box).
function Photo({ url, alt }) {
  return createElement("img", { src: url, alt });
}

// The same rules as the backend (UploadProfileImagesRequest).
const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/webp"];
const MAX_SIZE = 2 * 1024 * 1024; // 2 MB

// Top of the "Edit profile" page: the cover picture, the round picture
// and the name, like the profile design. Each picture is uploaded at once.
export function ProfileImagesEditor({ profile, onUploaded }) {
  const [error, setError] = useState("");
  const [uploading, setUploading] = useState(false);

  async function upload(field, file) {
    setError("");

    // Check in the browser first, so the user does not wait for nothing.
    if (!ALLOWED_TYPES.includes(file.type)) {
      setError(messageFor("notAnImage"));
      return;
    }
    if (file.size > MAX_SIZE) {
      setError(messageFor("imageTooBig"));
      return;
    }

    setUploading(true);
    try {
      const updated = await uploadProfileImages(profile.id, { [field]: file });
      onUploaded(updated);
    } catch (apiError) {
      setError(messageFor(readApiError(apiError).code));
    }
    setUploading(false);
  }

  return (
    <section className="profile-hero">
      <div className="profile-cover">
        {profile.cover_image && <Photo url={profile.cover_image} alt="Cover" />}
      </div>

      <div className="profile-hero-row">
        <div className="profile-avatar">
          {profile.profile_image ? (
            <Photo url={profile.profile_image} alt={profile.display_name} />
          ) : (
            <span aria-hidden="true">
              {profile.display_name.charAt(0).toUpperCase()}
            </span>
          )}
        </div>
        <h2>{profile.display_name}</h2>
      </div>

      {error && <p className="alert alert-error">{error}</p>}

      <div className="profile-hero-actions">
        <ImagePickerButton
          label={uploading ? "Uploading..." : "Change photo"}
          onPick={(file) => upload("profileImage", file)}
          disabled={uploading}
        />
        <ImagePickerButton
          label={uploading ? "Uploading..." : "Change cover"}
          onPick={(file) => upload("coverImage", file)}
          disabled={uploading}
        />
      </div>
      <p className="field-hint">JPG, PNG or WEBP, up to 2 MB.</p>
    </section>
  );
}
