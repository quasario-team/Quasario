import { messageFor } from "../messages";

// A big text box (several lines) with its label and its error message.
export function TextArea({ id, label, value, onChange, error, hint, ...rest }) {
  return (
    <div className="field">
      <label className="label" htmlFor={id}>
        {label}
      </label>
      <textarea
        id={id}
        className={error ? "textarea has-error" : "textarea"}
        value={value}
        onChange={(event) => onChange(event.target.value)}
        {...rest}
      />
      {hint && !error && <span className="field-hint">{hint}</span>}
      {error && <span className="field-error">{messageFor(error)}</span>}
    </div>
  );
}
