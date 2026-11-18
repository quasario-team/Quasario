// Every error code the backend can send, with the text the user sees.
// The codes come from Laravel (ApiFormRequest, DomainError, bootstrap/app.php).
const MESSAGES = {
  // Field errors (422)
  requiredField: "This field is required.",
  alreadyUsed: "This value is already used by another account.",
  weakPassword:
    "Password must be at least 8 characters, with letters and numbers.",
  wrongPassword: "The password is not correct.",
  invalidField: "This value is not valid.",

  // Whole-request errors
  validationFailed: "Please check the highlighted fields.",
  invalidCredentials: "Wrong username / email or password.",
  accountBanned: "This account is banned.",
  invalidToken:
    "This reset link is invalid or expired. Please ask for a new one.",
  adminNotAllowed: "An admin account cannot do this.",
  notAdmin: "Only an admin can open this page.",
  unauthenticated: "Your session has ended. Please sign in again.",
  tooManyRequests: "Too many tries. Please wait a minute and try again.",
  notFound: "Not found.",
  networkError: "Cannot reach the server. Check that Laravel is running.",
  unknownError: "Something went wrong. Please try again.",
};

export function messageFor(code) {
  return MESSAGES[code] ?? MESSAGES.unknownError;
}
