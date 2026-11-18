import { useContext } from "react";
import { AuthContext } from "./AuthContext";

// Pages call useAuth() to get: status, user, activeProfile, signIn, signOut ...
export function useAuth() {
  const auth = useContext(AuthContext);
  if (auth === null) {
    throw new Error("useAuth() must be used inside <AuthProvider>.");
  }
  return auth;
}
