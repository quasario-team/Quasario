import { useState } from "react";
import { AuthLayout } from "../components/AuthLayout";
import { AccountStep } from "./signup/AccountStep";

const EMPTY_ACCOUNT = { email: "", username: "", phone: "", password: "" };

// Use case U04 "Create Account": two parts on one page.
// Part 1 = account fields, part 2 = the first profile (step 8).
// Nothing is saved until the end of part 2.
export function SignUpPage() {
  const [part, setPart] = useState("account"); // 'account' or 'profile'
  const [account, setAccount] = useState(EMPTY_ACCOUNT);

  if (part === "profile") {
    // Temporary: the real profile part comes in step 8.
    return (
      <AuthLayout title="Your profile">
        <div className="form">
          <p className="alert alert-success">
            Account data is valid: {account.email}
          </p>
          <p className="auth-text">The profile part comes in step 8.</p>
          <button
            className="btn btn-outline btn-block"
            type="button"
            onClick={() => setPart("account")}
          >
            Back
          </button>
        </div>
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
        account={account}
        onChange={setAccount}
        onDone={() => setPart("profile")}
      />
    </AuthLayout>
  );
}
