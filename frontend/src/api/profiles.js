import { api } from "./client";

// Phase 2: the profiles of the signed-in account (/me/profiles).
// Laravel wraps each answer in { data: ... }, so we return response.data.data.

export async function getMyProfiles() {
  const response = await api.get("/me/profiles");
  return response.data.data; // a list, the active profile first
}

export async function createProfile(data) {
  // data = { display_name, job_category_id, job_title_id, city_id, bio?, study?, experience? }
  const response = await api.post("/me/profiles", data);
  return response.data.data;
}

export async function updateProfile(id, changes) {
  // changes = only the fields that changed
  const response = await api.patch(`/me/profiles/${id}`, changes);
  return response.data.data;
}

export async function deleteProfile(id) {
  await api.delete(`/me/profiles/${id}`);
}

export async function activateProfile(id) {
  // Makes this profile the active one; Laravel turns the others off.
  const response = await api.post(`/me/profiles/${id}/activate`);
  return response.data.data;
}

export async function uploadProfileImages(id, { profileImage, coverImage }) {
  // Files cannot be sent as JSON: we put them in a FormData "envelope".
  // axios sees the FormData and sends it as multipart/form-data by itself.
  const form = new FormData();
  if (profileImage) form.append("profile_image", profileImage);
  if (coverImage) form.append("cover_image", coverImage);
  const response = await api.post(`/me/profiles/${id}/images`, form);
  return response.data.data;
}

// ---- Contact links (feature 6) ----
export async function getProfileLinks(id) {
  const response = await api.get(`/me/profiles/${id}/links`);
  return response.data.data; // [{ platform, url }]
}

export async function saveProfileLinks(id, links) {
  // links = the WHOLE list [{ platform, url }]; an empty list removes all links.
  const response = await api.put(`/me/profiles/${id}/links`, { links });
  return response.data.data;
}

// ---- Work gallery (feature 7) ----
export async function getGallery(id) {
  const response = await api.get(`/me/profiles/${id}/gallery`);
  return response.data.data; // [{ id, image, description, created_at }], newest first
}

export async function addGalleryImage(id, file, description) {
  const form = new FormData();
  form.append("image", file);
  if (description) form.append("description", description);
  const response = await api.post(`/me/profiles/${id}/gallery`, form);
  return response.data.data;
}

export async function deleteGalleryImage(id, imageId) {
  await api.delete(`/me/profiles/${id}/gallery/${imageId}`);
}

// ---- Location (feature 8) ----
export async function getLocation(id) {
  const response = await api.get(`/me/profiles/${id}/location`);
  return response.data.data; // { latitude, longitude, place_image, directions } or null
}

export async function saveLocation(
  id,
  { latitude, longitude, directions, placeImage, removePlaceImage },
) {
  // POST (not PUT) because it can carry a picture. Laravel adds the location or changes it.
  const form = new FormData();
  form.append("latitude", latitude);
  form.append("longitude", longitude);
  form.append("directions", directions); // an empty text becomes null in Laravel
  if (placeImage) form.append("place_image", placeImage);
  if (removePlaceImage) form.append("remove_place_image", "1");
  const response = await api.post(`/me/profiles/${id}/location`, form);
  return response.data.data;
}

export async function deleteLocation(id) {
  await api.delete(`/me/profiles/${id}/location`);
}
