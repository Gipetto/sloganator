import type { FormEvent, ChangeEvent } from "react"
import "../styles/EditorForm.scss"

interface EditorFormProps {
  value: string
  cancelEditing: () => void
  handleSubmit: (e: FormEvent) => void
  handleChange: (e: ChangeEvent<HTMLInputElement>) => void
}

const EditorForm = ({
  value,
  cancelEditing,
  handleSubmit,
  handleChange,
}: EditorFormProps) => {
  const isDisabled = value.length === 0

  return (
    <form onSubmit={handleSubmit}>
      <fieldset>
        <input
          name="slogan"
          type="text"
          value={value}
          onChange={handleChange}
          autoComplete="off"
        />
        <button type="submit" className="slogan-submit" disabled={isDisabled}>
          Add Slogan
        </button>
        <button className="slogan-cancel" onClick={cancelEditing}>
          <span aria-hidden="true">&times;</span>
          <span>Cancel</span>
        </button>
      </fieldset>
    </form>
  )
}

export default EditorForm
