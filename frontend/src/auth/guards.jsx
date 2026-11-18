import { Navigate, Outlet } from "react-router-dom";
import { LoadingScreen } from "../components/LoadingScreen";
import { homePathFor } from "./homePath";
import { useAuth } from "./useAuth";

// Pages for guests only (sign in, sign up, forgot password).
// A signed-in person is sent to their home page.
export function GuestOnly() {
  const { status, user } = useAuth();

  if (status === "loading") return <LoadingScreen />;
  if (status === "signedIn") return <Navigate to={homePathFor(user)} replace />;
  return <Outlet />;
}

// Pages that need sign in (settings ...). A guest is sent to the sign in page.
export function RequireAuth() {
  const { status } = useAuth();

  if (status === "loading") return <LoadingScreen />;
  if (status === "guest") return <Navigate to="/sign-in" replace />;
  return <Outlet />;
}

// Admin panel pages. A guest goes to sign in, a normal user goes home.
export function RequireAdmin() {
  const { status, user } = useAuth();

  if (status === "loading") return <LoadingScreen />;
  if (status === "guest") return <Navigate to="/sign-in" replace />;
  if (user.role !== "admin") return <Navigate to="/" replace />;
  return <Outlet />;
}
