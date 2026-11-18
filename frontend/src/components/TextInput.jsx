import { createElement } from "react";
import { messageFor } from "../messages";

// One text box with its label and its error message.
// Every form in the site uses it, so all fields look and behave the same.
// error = an error code from the backend, e.g. 'requiredField' (or empty).
export function TextInput({
  id,
  label,
  value,
  onChange,
  error,
  hint,
  type = "text",
  ...rest
}) {
  const box = createElement("input", {
    id,
    type,
    value,
    className: error ? "input has-error" : "input",
    onChange: (event) => onChange(event.target.value),
    ...rest,
  });

  return (
    <div className="field">
      <label className="label" htmlFor={id}>
        {label}
      </label>
      {box}
      {hint && !error && <span className="field-hint">{hint}</span>}
      {error && <span className="field-error">{messageFor(error)}</span>}
    </div>
  );
}
