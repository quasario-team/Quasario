import { createElement } from "react";

// A button that opens the "choose a picture" window of the computer / phone.
// It is a label that holds a hidden file box: clicking the label opens the box.
export function ImagePickerButton({ label, onPick, disabled }) {
  const hiddenBox = createElement("input", {
    type: "file",
    accept: "image/jpeg,image/png,image/webp",
    hidden: true,
    disabled,
    onChange: (event) => {
      const file = event.target.files[0];
      // Empty the box, so choosing the same file again still works.
      event.target.value = "";
      if (file) onPick(file);
    },
  });

  return (
    <label
      className={disabled ? "btn btn-outline is-disabled" : "btn btn-outline"}
    >
      {label}
      {hiddenBox}
    </label>
  );
}
