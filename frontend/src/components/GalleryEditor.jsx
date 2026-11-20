import { createElement, useEffect, useState } from "react";
import { readApiError } from "../api/client";
import {
  addGalleryImage,
  deleteGalleryImage,
  getGallery,
} from "../api/profiles";
import { messageFor } from "../messages";
import { ConfirmDialog } from "./ConfirmDialog";
import { ImagePickerButton } from "./ImagePickerButton";
import { TextInput } from "./TextInput";

// The same rules as the backend (StoreGalleryImageRequest, GalleryImage::MAX_PER_PROFILE).
const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/webp"];
const MAX_SIZE = 2 * 1024 * 1024; // 2 MB
const MAX_PICTURES = 30;

// A photo tag (built with createElement, like the TextInput box).
function Photo({ url, alt }) {
  return createElement("img", { src: url, alt });
}

// "Work gallery" box of the Edit profile page (phase 2, feature 7).
export function GalleryEditor({ profileId }) {
  const [pictures, setPictures] = useState(null); // null = still loading
  const [description, setDescription] = useState("");
  const [error, setError] = useState("");
  const [uploading, setUploading] = useState(false);
  const [toDelete, setToDelete] = useState(null);
  const [deleting, setDeleting] = useState(false);

  useEffect(() => {
    getGallery(profileId)
      .then(setPictures)
      .catch((apiError) => {
        setError(messageFor(readApiError(apiError).code));
        setPictures([]);
      });
  }, [profileId]);

  async function handlePick(file) {
    setError("");
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
      const added = await addGalleryImage(profileId, file, description.trim());
      // The newest picture goes first, like the list from Laravel.
      setPictures((current) => [added, ...current]);
      setDescription("");
    } catch (apiError) {
      // galleryFull, invalidField, notFound ...
      setError(messageFor(readApiError(apiError).code));
    }
    setUploading(false);
  }

  async function handleDelete() {
    setError("");
    setDeleting(true);
    try {
      await deleteGalleryImage(profileId, toDelete.id);
      setPictures((current) =>
        current.filter((picture) => picture.id !== toDelete.id),
      );
    } catch (apiError) {
      setError(messageFor(readApiError(apiError).code));
    }
    setDeleting(false);
    setToDelete(null);
  }

  const isFull = pictures !== null && pictures.length >= MAX_PICTURES;

  return (
    <section className="panel">
      <h2>Work gallery</h2>
      {error && <p className="alert alert-error">{error}</p>}

      {pictures === null ? (
        <p className="muted-text">Loading...</p>
      ) : (
        <>
          {pictures.length === 0 && (
            <p className="muted-text">
              No pictures yet. Show people your work!
            </p>
          )}

          <div className="gallery-grid">
            {pictures.map((picture) => (
              <div className="gallery-item" key={picture.id}>
                <Photo
                  url={picture.image}
                  alt={picture.description ?? "Work picture"}
                />
                <button
                  className="gallery-delete"
                  type="button"
                  aria-label="Delete picture"
                  onClick={() => setToDelete(picture)}
                >
                  ×
                </button>
                {picture.description && (
                  <p className="gallery-caption">{picture.description}</p>
                )}
              </div>
            ))}
          </div>

          {isFull ? (
            <p className="muted-text">
              The gallery is full ({MAX_PICTURES} pictures).
            </p>
          ) : (
            <div className="form gallery-add">
              <TextInput
                id="gallery_description"
                label="Description of the next picture (optional)"
                maxLength={500}
                value={description}
                onChange={setDescription}
              />
              <ImagePickerButton
                label={uploading ? "Uploading..." : "Add picture"}
                onPick={handlePick}
                disabled={uploading}
              />
            </div>
          )}
          <p className="field-hint text-end">
            {pictures.length} / {MAX_PICTURES} pictures · JPG, PNG or WEBP, up
            to 2 MB
          </p>
        </>
      )}

      {toDelete && (
        <ConfirmDialog
          title="Delete this picture?"
          text="It will be removed from your gallery. This cannot be undone."
          confirmLabel="Delete"
          busyLabel="Deleting..."
          busy={deleting}
          onCancel={() => setToDelete(null)}
          onConfirm={handleDelete}
        />
      )}
    </section>
  );
}
