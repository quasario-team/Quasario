// Error codes come from the Laravel API. We show a friendly sentence for each one.
const messages = {
  invalidCredentials: 'Email or password is incorrect.',
  accountBanned: 'This account is banned.',
  tooManyRequests: 'Too many attempts. Wait a minute and try again.',
  validationFailed: 'Please fix the fields marked in red.',
  requiredField: 'This field is required.',
  alreadyUsed: 'This value is already used.',
  weakPassword: 'At least 8 characters, with a letter and a number.',
  invalidField: 'This value is not valid.',
}

// One message for the whole form.
export function formError(error) {
  if (!error.response) {
    return 'Cannot reach the server. Is the backend running?'
  }
  return messages[error.response.data?.code] ?? 'Something went wrong. Try again.'
}

// A message for each field, e.g. { email: 'This value is already used.' }
export function fieldErrors(error) {
  const errors = error.response?.data?.errors ?? {}
  const result = {}
  for (const field in errors) {
    result[field] = messages[errors[field][0]] ?? messages.invalidField
  }
  return result
}
