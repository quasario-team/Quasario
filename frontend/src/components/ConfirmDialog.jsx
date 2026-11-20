// A small "Are you sure?" window, used before deleting something.
// The page decides the texts, and what happens on Cancel and on Confirm.
export function ConfirmDialog({
  title,
  text,
  confirmLabel,
  busyLabel,
  busy,
  onCancel,
  onConfirm,
}) {
  return (
    <div className="dialog-backdrop" role="presentation">
      <div
        className="dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-title"
      >
        <h2 id="confirm-title">{title}</h2>
        <p>{text}</p>
        <div className="dialog-actions">
          <button
            className="btn btn-outline"
            type="button"
            onClick={onCancel}
            disabled={busy}
          >
            Cancel
          </button>
          <button
            className="btn btn-danger"
            type="button"
            onClick={onConfirm}
            disabled={busy}
          >
            {busy ? busyLabel : confirmLabel}
          </button>
        </div>
      </div>
    </div>
  );
}
