import { Route, Routes } from "react-router-dom";
import { GuestOnly, RequireAdmin, RequireAuth } from "./auth/guards";
import { AccountSettingsPage } from "./pages/AccountSettingsPage";
import { AdminDashboardPage } from "./pages/AdminDashboardPage";
import { ChangePasswordPage } from "./pages/ChangePasswordPage";
import { ForgotPasswordPage } from "./pages/ForgotPasswordPage";
import { HomePage } from "./pages/HomePage";
import { MyProfilesPage } from "./pages/MyProfilesPage";
import { NotFoundPage } from "./pages/NotFoundPage";
import { OnboardingPage } from "./pages/OnboardingPage";
import { ProfileFormPage } from "./pages/ProfileFormPage";
import { ResetPasswordPage } from "./pages/ResetPasswordPage";
import { SettingsPage } from "./pages/SettingsPage";
import { SignInPage } from "./pages/SignInPage";
import { SignUpPage } from "./pages/SignUpPage";

// The map of the site: every address and the page it shows.
// Phase 2 adds the profile pages.
export default function App() {
  return (
    <Routes>
      {/* Everyone */}
      <Route path="/" element={<HomePage />} />
      {/* The email link opens this page, so it works signed in or not. */}
      <Route path="/reset-password" element={<ResetPasswordPage />} />

      {/* Guests only */}
      <Route element={<GuestOnly />}>
        <Route path="/sign-in" element={<SignInPage />} />
        <Route path="/sign-up" element={<SignUpPage />} />
        <Route path="/forgot-password" element={<ForgotPasswordPage />} />
      </Route>

      {/* Signed in */}
      <Route element={<RequireAuth />}>
        <Route path="/settings" element={<SettingsPage />} />
        <Route path="/settings/account" element={<AccountSettingsPage />} />
        <Route path="/settings/password" element={<ChangePasswordPage />} />
        <Route path="/profiles" element={<MyProfilesPage />} />
        <Route path="/profiles/new" element={<ProfileFormPage />} />
        <Route path="/profiles/:id/edit" element={<ProfileFormPage />} />
        <Route path="/onboarding" element={<OnboardingPage />} />
      </Route>

      {/* Admin only */}
      <Route element={<RequireAdmin />}>
        <Route path="/admin" element={<AdminDashboardPage />} />
      </Route>

      <Route path="*" element={<NotFoundPage />} />
    </Routes>
  );
}
