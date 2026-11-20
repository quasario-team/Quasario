import { useCallback, useEffect, useMemo, useState } from "react";
import * as authApi from "../api/auth";
import { onSessionEnded, tokenStorage } from "../api/client";
import { AuthContext } from "./AuthContext";

const SIGNED_OUT = { status: "guest", user: null, activeProfile: null };

// Keeps "who is signed in" for the whole site.
// status: 'loading' (checking the saved token) | 'guest' | 'signedIn'
export function AuthProvider({ children }) {
  const [session, setSession] = useState(() =>
    tokenStorage.get() ? { ...SIGNED_OUT, status: "loading" } : SIGNED_OUT,
  );
  // Why the last session ended by itself (e.g. 'accountBanned'), to show a message.
  const [endedReason, setEndedReason] = useState(null);

  // When the site opens with a saved token: ask Laravel who we are.
  useEffect(() => {
    if (!tokenStorage.get()) return;

    authApi
      .getMe()
      .then((me) =>
        setSession({
          status: "signedIn",
          user: me.user,
          activeProfile: me.active_profile,
        }),
      )
      // A bad token is already removed by api/client.js. If the server is off,
      // we keep the token and the user is signed in again on the next visit.
      .catch(() => setSession(SIGNED_OUT));
  }, []);

  // If the token stops working on any request, show the signed-out state.
  useEffect(() => {
    onSessionEnded((reason) => {
      setSession(SIGNED_OUT);
      setEndedReason(reason);
    });
  }, []);

  const signIn = useCallback(async (loginValue, password) => {
    const result = await authApi.login(loginValue, password);
    tokenStorage.set(result.token);
    setEndedReason(null);
    setSession({
      status: "signedIn",
      user: result.user,
      activeProfile: result.active_profile,
    });
    return result.user;
  }, []);

  // Narrative U21 (2a): if the server cannot be reached, show an error and stay
  // signed in. So we only forget the token after Laravel said "done".
  const signOut = useCallback(async () => {
    await authApi.logout();
    tokenStorage.clear();
    setSession(SIGNED_OUT);
  }, []);

  // Used after "account deleted": the token is already gone on the server.
  const forgetSession = useCallback(() => {
    tokenStorage.clear();
    setSession(SIGNED_OUT);
  }, []);

  // Used after the account settings are saved.
  const updateUser = useCallback((user) => {
    setSession((current) => ({ ...current, user }));
  }, []);

  // Phase 2: ask Laravel again "who am I?", e.g. after a profile was
  // deleted, so the home page shows the profile that is active now.
  const refreshMe = useCallback(async () => {
    const me = await authApi.getMe();
    setSession((current) => ({
      ...current,
      user: me.user,
      activeProfile: me.active_profile,
    }));
  }, []);

  const value = useMemo(
    () => ({
      ...session,
      endedReason,
      signIn,
      signOut,
      forgetSession,
      updateUser,
      refreshMe,
    }),
    [
      session,
      endedReason,
      signIn,
      signOut,
      forgetSession,
      updateUser,
      refreshMe,
    ],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}
