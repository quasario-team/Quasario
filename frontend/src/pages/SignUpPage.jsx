import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { AuthLayout } from "../components/AuthLayout";
import { AccountStep } from "./signup/AccountStep";
import { ProfileStep } from "./signup/ProfileStep";

const EMPTY_ACCOUNT = { email: "", username: "", phone: "", password: "" };
const EMPTY_PROFILE = {
  job_category_id: "",
  job_title_id: "",
  city_id: "",
  experience: "",
};

// Use case U04 "Create Account": two parts on one page.
// Part 1 = account fields, part 2 = the first profile.
// Nothing is saved until "Finish" in part 2 (one transaction in Laravel).
export function SignUpPage() {
  const navigate = useNavigate();
  const [part, setPart] = useState("account"); // 'account' or 'profile'
  const [account, setAccount] = useState(EMPTY_ACCOUNT);
  const [profile, setProfile] = useState(EMPTY_PROFILE);
  // Errors that part 2 sends back to part 1 (line 8a).
  const [accountErrors, setAccountErrors] = useState({});

  function showAccountErrors(errors) {
    setAccountErrors(errors);
    setPart("account");
  }

  function finish() {
    // Line 9: no automatic sign in. Show a green message on the Sign in page.
    navigate("/sign-in", {
      replace: true,
      state: { notice: "Your account was created. Please sign in." },
    });
  }

  if (part === "profile") {
    return (
      <AuthLayout title="Your profile">
        <ProfileStep
          account={account}
          profile={profile}
          onChange={setProfile}
          onBack={() => setPart("account")} // line 6a: nothing is created
          onAccountErrors={showAccountErrors}
          onDone={finish}
        />
      </AuthLayout>
    );
  }

  return (
    <AuthLayout
      title="Sign up"
      switchText="Have an Account ?"
      switchLabel="Sign in"
      switchTo="/sign-in"
    >
      <AccountStep
        key={JSON.stringify(accountErrors)}
        account={account}
        onChange={setAccount}
        initialErrors={accountErrors}
        onDone={() => {
          setAccountErrors({});
          setPart("profile");
        }}
      />
    </AuthLayout>
  );
}
