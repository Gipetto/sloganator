import type { FormEvent, ChangeEvent } from "react"
import ExpandingTextArea from "./ExpandingTextArea"
import "../styles/EditorForm.scss"

interface EditorFormProps {
  value: string
  cancelEditing: (e: FormEvent) => void
  handleSubmit: (e: FormEvent) => void
  handleChange: (e: ChangeEvent<HTMLTextAreaElement>) => void
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
        <ExpandingTextArea
          autoComplete="off"
          spellCheck="false"
          maxLength={250}
          name="slogan"
          onChange={handleChange}
          placeholder="Gimme yer best shot!"
          value={value}
        />
        <div className="controls">
          <button
            type="submit"
            className="slogan-submit command-button primary"
            disabled={isDisabled}
          >
            Add Slogan
          </button>
          <button
            className="slogan-cancel command-button"
            onClick={cancelEditing}
          >
            <span>Cancel</span>
          </button>
        </div>
      </fieldset>
    </form>
  )
}

export default EditorForm
