import { useEffect, useState } from "react";
import { readApiError } from "../api/client";
import { getProfileLinks, saveProfileLinks } from "../api/profiles";
import { messageFor } from "../messages";
import { TextInput } from "./TextInput";

// The 9 platforms of the backend (SocialLink::PLATFORMS), in the same order,
// with the name the user sees and an example address.
const PLATFORMS = [
  { key: "whatsapp", name: "WhatsApp", example: "https://wa.me/963900000000" },
  {
    key: "facebook",
    name: "Facebook",
    example: "https://facebook.com/your.page",
  },
  {
    key: "linkedin",
    name: "LinkedIn",
    example: "https://linkedin.com/in/your-name",
  },
  {
    key: "youtube",
    name: "YouTube",
    example: "https://youtube.com/@your-channel",
  },
  { key: "x", name: "X", example: "https://x.com/your_name" },
  {
    key: "instagram",
    name: "Instagram",
    example: "https://instagram.com/your_name",
  },
  { key: "telegram", name: "Telegram", example: "https://t.me/your_name" },
  { key: "github", name: "GitHub", example: "https://github.com/your-name" },
  { key: "website", name: "Website", example: "https://your-site.com" },
];

const EMPTY_LINKS = Object.fromEntries(
  PLATFORMS.map((platform) => [platform.key, ""]),
);

// "Contact links" box of the Edit profile page (phase 2, feature 6).
// One text box per platform; an empty box means "no link".
export function SocialLinksEditor({ profileId }) {
  const [links, setLinks] = useState(EMPTY_LINKS); // { whatsapp: 'https://...', ... }
  const [loaded, setLoaded] = useState(false);
  const [fieldErrors, setFieldErrors] = useState({});
  const [formError, setFormError] = useState("");
  const [notice, setNotice] = useState("");
  const [sending, setSending] = useState(false);

  // Fill the boxes with the saved links.
  useEffect(() => {
    getProfileLinks(profileId)
      .then((saved) => {
        const values = { ...EMPTY_LINKS };
        for (const link of saved) values[link.platform] = link.url;
        setLinks(values);
        setLoaded(true);
      })
      .catch((error) => setFormError(messageFor(readApiError(error).code)));
  }, [profileId]);

  function setLink(platform, value) {
    setLinks((current) => ({ ...current, [platform]: value }));
    setFieldErrors((current) => ({ ...current, [platform]: undefined }));
    setNotice("");
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setFormError("");
    setNotice("");

    // Only the filled boxes are sent, in the order of PLATFORMS.
    const filled = PLATFORMS.map((platform) => ({
      platform: platform.key,
      url: links[platform.key].trim(),
    })).filter((link) => link.url !== "");

    setSending(true);
    try {
      await saveProfileLinks(profileId, filled);
      setFieldErrors({});
      setNotice("Your links were saved.");
    } catch (error) {
      const { code, fields } = readApiError(error);
      // Laravel names the errors by position: "links.2.url" = the 3rd sent link.
      // We turn the position back into the platform, to show the error under its box.
      const errors = {};
      for (const [name, errorCode] of Object.entries(fields)) {
        const position = Number(name.split(".")[1]);
        if (filled[position]) errors[filled[position].platform] = errorCode;
      }
      setFieldErrors(errors);
      setFormError(messageFor(code));
    }
    setSending(false);
  }

  return (
    <form className="form panel" onSubmit={handleSubmit} noValidate>
      <h2>Contact links</h2>
      <p className="field-hint">
        Leave a box empty if you do not use that platform.
      </p>

      {formError && <p className="alert alert-error">{formError}</p>}
      {notice && <p className="alert alert-success">{notice}</p>}

      {PLATFORMS.map((platform) => (
        <TextInput
          key={platform.key}
          id={`link_${platform.key}`}
          type="url"
          label={platform.name}
          placeholder={platform.example}
          maxLength={2048}
          value={links[platform.key]}
          onChange={(value) => setLink(platform.key, value)}
          error={fieldErrors[platform.key]}
        />
      ))}

      <button
        className="btn btn-primary btn-block"
        type="submit"
        disabled={sending || !loaded}
      >
        {sending ? "Saving..." : "Save links"}
      </button>
    </form>
  );
}
