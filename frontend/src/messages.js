// Every error code the backend can send, with the text the user sees.
// The codes come from Laravel (ApiFormRequest, DomainError, bootstrap/app.php).
const MESSAGES = {
  // Field errors (422)
  requiredField: "This field is required.",
  alreadyUsed: "This value is already used by another account.",
  weakPassword:
    "Password must be at least 8 characters, with letters and numbers.",
  wrongPassword: "The password is not correct.",
  passwordsDoNotMatch: "The two passwords are not the same.", // checked in the browser only
  notAnImage: "Please choose a JPG, PNG or WEBP picture.", // checked in the browser only
  imageTooBig: "The picture is bigger than 2 MB.", // checked in the browser only
  invalidField: "This value is not valid.",
  wrongPlatformUrl: "This address is not a link of this platform.",
  locationNotAvailable:
    "Your browser could not give your location. Type the numbers instead.", // browser only

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
  profileLimitReached: "An account can have at most 5 profiles.",
  lastProfile: "You cannot delete your only profile.",
  galleryFull: "Your gallery is full (30 pictures). Delete one to add another.",
  networkError: "Cannot reach the server. Check that Laravel is running.",
  unknownError: "Something went wrong. Please try again.",
};

export function messageFor(code) {
  return MESSAGES[code] ?? MESSAGES.unknownError;
}
