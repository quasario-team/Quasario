import { messageFor } from "../messages";

// A drop-down list with its label and its error message.
// options = [{ id, name }], placeholder = the first, empty choice.
export function SelectInput({
  id,
  label,
  value,
  onChange,
  options,
  placeholder,
  error,
  disabled,
}) {
  return (
    <div className="field">
      <label className="label" htmlFor={id}>
        {label}
      </label>
      <select
        id={id}
        className={error ? "select has-error" : "select"}
        value={value}
        onChange={(event) => onChange(event.target.value)}
        disabled={disabled}
      >
        <option value="">{placeholder}</option>
        {options.map((option) => (
          <option key={option.id} value={option.id}>
            {option.name}
          </option>
        ))}
      </select>
      {error && <span className="field-error">{messageFor(error)}</span>}
    </div>
  );
}
