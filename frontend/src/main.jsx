import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
// Poppins font (installed with npm, so it works without internet links in the page).
import "@fontsource/poppins/400.css";
import "@fontsource/poppins/500.css";
import "@fontsource/poppins/600.css";
import "./index.css";
import App from "./App.jsx";
import { AuthProvider } from "./auth/AuthProvider";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <AuthProvider>
      <App />
    </AuthProvider>
  </StrictMode>,
);
