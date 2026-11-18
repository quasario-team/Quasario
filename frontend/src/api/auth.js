import { api } from "./client";

// One small function for each backend route of phase 1.
// Each one returns the JSON that Laravel sends back.

// ---- Sign up ----
export async function getRegistrationLookups() {
  const response = await api.get("/lookups/registration");
  return response.data; // { cities, job_categories }
}

export async function checkRegistration(account) {
  // account = { email, username, phone, password }. Saves nothing.
  await api.post("/auth/register/check", account);
}

export async function register(data) {
  // data = account fields + job_category_id, job_title_id, city_id, experience
  const response = await api.post("/auth/register", data);
  return response.data;
}

// ---- Sign in / sign out ----
export async function login(loginValue, password) {
  const response = await api.post("/auth/login", {
    login: loginValue,
    password,
  });
  return response.data; // { token, user, active_profile }
}

export async function logout() {
  await api.post("/auth/logout");
}

export async function getMe() {
  const response = await api.get("/me");
  return response.data; // { user, active_profile, profiles_count }
}

// ---- Forgot / reset password ----
export async function forgotPassword(email) {
  await api.post("/auth/forgot-password", { email });
}

export async function resetPassword(data) {
  // data = { email, token, password }
  await api.post("/auth/reset-password", data);
}

// ---- Account settings ----
export async function updateAccount(changes) {
  // changes = only the fields that changed: { email?, username?, phone? }
  const response = await api.patch("/me/account", changes);
  return response.data.user;
}

export async function changePassword(currentPassword, newPassword) {
  await api.patch("/me/password", {
    current_password: currentPassword,
    password: newPassword,
  });
}

export async function deleteAccount(password) {
  await api.delete("/me", { data: { password } });
}
