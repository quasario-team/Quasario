import { createElement, useEffect, useState } from "react";
import { readApiError } from "../api/client";
import { deleteLocation, getLocation, saveLocation } from "../api/profiles";
import { messageFor } from "../messages";
import { ConfirmDialog } from "./ConfirmDialog";
import { ImagePickerButton } from "./ImagePickerButton";
import { TextArea } from "./TextArea";
import { TextInput } from "./TextInput";

// The same rules as the backend (SaveLocationRequest).
const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/webp"];
const MAX_SIZE = 2 * 1024 * 1024; // 2 MB
const LIMITS = { latitude: 90, longitude: 180 };
const EMPTY_FORM = { latitude: "", longitude: "", directions: "" };

// A photo tag (built with createElement, like the TextInput box).
function Photo({ url, alt }) {
  return createElement("img", { src: url, alt });
}

// Turn a saved location into the values of the form (all strings).
function formFromLocation(location) {
  if (!location) return EMPTY_FORM;
  return {
    latitude: String(location.latitude),
    longitude: String(location.longitude),
    directions: location.directions ?? "",
  };
}

// Check one number box: empty -> requiredField, not a number or too big -> invalidField.
function checkNumber(text, limit) {
  if (text.trim() === "") return "requiredField";
  const number = Number(text);
  if (Number.isNaN(number) || Math.abs(number) > limit) return "invalidField";
  return undefined;
}

// "Location" box of the Edit profile page (phase 2, feature 8).
// The map comes in phase 3; here the user types the numbers or takes them from the browser.
export function LocationEditor({ profileId }) {
  const [saved, setSaved] = useState(null); // the location in Laravel, or null
  const [loaded, setLoaded] = useState(false);
  const [form, setForm] = useState(EMPTY_FORM);
  const [placeFile, setPlaceFile] = useState(null); // a new picture, not sent yet
  const [removeImage, setRemoveImage] = useState(false);
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [notice, setNotice] = useState("");
  const [sending, setSending] = useState(false);
  const [askDelete, setAskDelete] = useState(false);
  const [deleting, setDeleting] = useState(false);

  useEffect(() => {
    getLocation(profileId)
      .then((location) => {
        setSaved(location);
        setForm(formFromLocation(location));
        setLoaded(true);
      })
      .catch((error) => setFormError(messageFor(readApiError(error).code)));
  }, [profileId]);

  function setField(name, value) {
    setForm((current) => ({ ...current, [name]: value }));
    setFieldErrors((current) => ({ ...current, [name]: undefined }));
    setNotice("");
  }

  // Ask the browser where we are (it shows "Allow location?" to the user).
  function fillMyLocation() {
    setFormError("");
    if (!navigator.geolocation) {
      setFormError(messageFor("locationNotAvailable"));
      return;
    }
    navigator.geolocation.getCurrentPosition(
      (position) => {
        setField("latitude", position.coords.latitude.toFixed(7));
        setField("longitude", position.coords.longitude.toFixed(7));
      },
      () => setFormError(messageFor("locationNotAvailable")),
    );
  }

  function handlePick(file) {
    setFormError("");
    if (!ALLOWED_TYPES.includes(file.type)) {
      setFormError(messageFor("notAnImage"));
      return;
    }
    if (file.size > MAX_SIZE) {
      setFormError(messageFor("imageTooBig"));
      return;
    }
    setPlaceFile(file);
    setRemoveImage(false);
    setNotice("");
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");
    setNotice("");

    const errors = {};
    for (const name of Object.keys(LIMITS)) {
      const code = checkNumber(form[name], LIMITS[name]);
      if (code) errors[name] = code;
    }
    setFieldErrors(errors);
    if (Object.keys(errors).length > 0) return;

    setSending(true);
    try {
      const location = await saveLocation(profileId, {
        latitude: form.latitude.trim(),
        longitude: form.longitude.trim(),
        directions: form.directions.trim(),
        placeImage: placeFile,
        removePlaceImage: removeImage,
      });
      setSaved(location);
      setForm(formFromLocation(location));
      setPlaceFile(null);
      setRemoveImage(false);
      setNotice("Your location was saved.");
    } catch (error) {
      const { code, fields } = readApiError(error);
      setFieldErrors(fields);
      setFormError(messageFor(code));
    }
    setSending(false);
  }

  async function handleDelete() {
    setFormError("");
    setDeleting(true);
    try {
      await deleteLocation(profileId);
      setSaved(null);
      setForm(EMPTY_FORM);
      setPlaceFile(null);
      setRemoveImage(false);
      setNotice("Your location was removed.");
    } catch (error) {
      setFormError(messageFor(readApiError(error).code));
    }
    setDeleting(false);
    setAskDelete(false);
  }

  const showSavedPhoto = saved?.place_image && !removeImage && !placeFile;
  const mapAddress = saved
    ? `https://www.google.com/maps?q=${saved.latitude},${saved.longitude}`
    : null;

  return (
    <form className="form panel" onSubmit={handleSubmit} noValidate>
      <h2>Location</h2>
      <p className="field-hint">
        Where people can find you. The map comes in a later version.
      </p>

      {formError && <p className="alert alert-error">{formError}</p>}
      {notice && <p className="alert alert-success">{notice}</p>}

      <button
        className="btn btn-outline"
        type="button"
        onClick={fillMyLocation}
        disabled={!loaded}
      >
        Use my current location
      </button>

      <div className="field-row">
        <TextInput
          id="latitude"
          label="Latitude"
          inputMode="decimal"
          placeholder="33.5138"
          value={form.latitude}
          onChange={(value) => setField("latitude", value)}
          error={fieldErrors.latitude}
        />
        <TextInput
          id="longitude"
          label="Longitude"
          inputMode="decimal"
          placeholder="36.2765"
          value={form.longitude}
          onChange={(value) => setField("longitude", value)}
          error={fieldErrors.longitude}
        />
      </div>

      {mapAddress && (
        <a
          className="link-button"
          href={mapAddress}
          target="_blank"
          rel="noreferrer"
        >
          Open the saved place in Google Maps ↗
        </a>
      )}

      <TextArea
        id="directions"
        label="Directions (optional)"
        placeholder="Second floor, next to the bakery"
        maxLength={1000}
        value={form.directions}
        onChange={(value) => setField("directions", value)}
        error={fieldErrors.directions}
      />

      <div className="field">
        <span className="label">Photo of the place (optional)</span>
        {showSavedPhoto && (
          <div className="location-photo">
            <Photo url={saved.place_image} alt="The place" />
          </div>
        )}
        {placeFile && (
          <span className="field-hint">
            New photo: {placeFile.name} (saved when you press Save)
          </span>
        )}
        {removeImage && (
          <span className="field-hint">
            The photo will be removed when you press Save.
          </span>
        )}
        {fieldErrors.place_image && (
          <span className="field-error">
            {messageFor(fieldErrors.place_image)}
          </span>
        )}
        <div className="dialog-actions">
          <ImagePickerButton
            label={showSavedPhoto ? "Change photo" : "Choose photo"}
            onPick={handlePick}
            disabled={sending}
          />
          <button
            className="btn btn-danger-outline"
            type="button"
            onClick={() => {
              setPlaceFile(null);
              setRemoveImage(Boolean(saved?.place_image));
            }}
            disabled={sending || (!showSavedPhoto && !placeFile)}
          >
            Remove photo
          </button>
        </div>
      </div>

      <div className="dialog-actions">
        <button
          className="btn btn-danger-outline"
          type="button"
          onClick={() => setAskDelete(true)}
          disabled={sending || !saved}
        >
          Delete location
        </button>
        <button
          className="btn btn-primary"
          type="submit"
          disabled={sending || !loaded}
        >
          {sending ? "Saving..." : "Save location"}
        </button>
      </div>

      {askDelete && (
        <ConfirmDialog
          title="Delete your location?"
          text="People will not see where you work any more."
          confirmLabel="Delete"
          busyLabel="Deleting..."
          busy={deleting}
          onCancel={() => setAskDelete(false)}
          onConfirm={handleDelete}
        />
      )}
    </form>
  );
}
