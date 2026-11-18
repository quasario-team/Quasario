import { createContext } from "react";

// A shared box: every page can read who is signed in from here.
export const AuthContext = createContext(null);
