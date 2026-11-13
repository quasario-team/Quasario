// One input with its label and its red error message.
export default function Field({ label, error, hint, ...inputProps }) {
  return (
    <label className="field">
      <span className="field-label">{label}</span>
      <input className={error ? 'input has-error' : 'input'} {...inputProps} />
      {error ? <span className="field-error">{error}</span> : hint && <span className="field-hint">{hint}</span>}
    </label>
  )
}
