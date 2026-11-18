import { Route, Routes } from "react-router-dom";
import { GuestOnly, RequireAdmin, RequireAuth } from "./auth/guards";
import { ComingSoonPage } from "./pages/ComingSoonPage";
import { NotFoundPage } from "./pages/NotFoundPage";
import { SignInPage } from "./pages/SignInPage";
import { SignUpPage } from "./pages/SignUpPage";

// The map of the site: every address and the page it shows.
// Each ComingSoonPage is replaced by the real page in its own step.
export default function App() {
  return (
    <Routes>
      {/* Everyone */}
      <Route path="/" element={<ComingSoonPage title="Home" />} />
      {/* The email link opens this page, so it works signed in or not. */}
      <Route
        path="/reset-password"
        element={<ComingSoonPage title="Reset password" />}
      />

      {/* Guests only */}
      <Route element={<GuestOnly />}>
        <Route path="/sign-in" element={<SignInPage />} />
        <Route path="/sign-up" element={<SignUpPage />} />
        <Route
          path="/forgot-password"
          element={<ComingSoonPage title="Forgot password" />}
        />
      </Route>

      {/* Signed in */}
      <Route element={<RequireAuth />}>
        <Route path="/settings" element={<ComingSoonPage title="Settings" />} />
        <Route
          path="/settings/account"
          element={<ComingSoonPage title="Account settings" />}
        />
      </Route>

      {/* Admin only */}
      <Route element={<RequireAdmin />}>
        <Route
          path="/admin"
          element={<ComingSoonPage title="Admin dashboard" />}
        />
      </Route>

      <Route path="*" element={<NotFoundPage />} />
    </Routes>
  );
}
