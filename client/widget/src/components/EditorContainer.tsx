import { useState, useEffect, useRef } from "react"
import type { FormEvent, ChangeEvent } from "react"
import PreviousSlogans from "./PreviousSlogans"
import EditorForm from "./EditorForm"
import { useSlogansContext } from "../contexts/SlogansContext"
import "../styles/EditorContainer.scss"
import { Dialog, Panel, SystemError } from "./EightDotOne"

const initCloseListener = (
  widgetRef: React.RefObject<HTMLDivElement>,
  setEditing: (b: boolean) => void
) => {
  const handleClick = (event: MouseEvent) => {
    if (
      widgetRef?.current &&
      !widgetRef.current.contains(event.target as HTMLElement)
    ) {
      setEditing(false)
    }
  }

  const handleKeyUp = (event: KeyboardEvent) => {
    if (
      widgetRef?.current &&
      (event.target as HTMLElement)?.nodeName !== "TEXTAREA" &&
      event.key === "Escape"
    ) {
      setEditing(false)
    }
  }

  useEffect(() => {
    document.addEventListener("mousedown", handleClick)
    document.addEventListener("keyup", handleKeyUp)

    return () => {
      document.removeEventListener("mousedown", handleClick)
      document.removeEventListener("keyup", handleKeyUp)
    }
  }, [widgetRef])
}

const EditorContainer = () => {
  const [value, setValue] = useState("")
  const {
    state: { editing, slogans, submitError },
    actions: { setEditing, setSubmitError, addSlogan, reset },
  } = useSlogansContext()

  const widgetRef = useRef(null)
  initCloseListener(widgetRef, setEditing)

  const handleChange = (e: ChangeEvent<HTMLTextAreaElement>) => {
    setValue(e.target.value)
  }

  const handleSubmit = (e: FormEvent) => {
    e.stopPropagation()
    e.preventDefault()
    addSlogan(value)
  }

  const handleCancel = (e: FormEvent | MouseEvent) => {
    e.stopPropagation()
    e.preventDefault()
    setEditing(false)
    setValue("")
  }

  const forceSystemError = (e: React.MouseEvent<HTMLElement>) => {
    e.stopPropagation()
    e.preventDefault()
    setSubmitError({ code: "ID10T", message: "Don't touch that!" })
  }

  const copySlogan = (slogan: string) => {
    setValue(slogan)
  }

  return (
    <div className="slogan-editor" ref={widgetRef}>
      {submitError && (
        <SystemError
          error={submitError}
          onClose={() => {
            setEditing(false)
            reset()
          }}
        />
      )}

      {editing && !submitError && (
        <Dialog
          title="Sloganator"
          onClose={handleCancel}
          onZoom={forceSystemError}
          onWindowshade={forceSystemError}
        >
          <Panel>
            <EditorForm
              value={value}
              handleSubmit={handleSubmit}
              handleChange={handleChange}
              cancelEditing={handleCancel}
            />
            <div>
              <p>
                <span style={{ float: "right" }}>
                  <a target="_blank" href="/mies/sloganator">
                    Sloganator &#8599;
                  </a>
                </span>
                <span>Recent Slogans:</span>
              </p>
            </div>
          </Panel>
          <PreviousSlogans slogans={slogans} copySlogan={copySlogan} />
        </Dialog>
      )}
    </div>
  )
}

export default EditorContainer
