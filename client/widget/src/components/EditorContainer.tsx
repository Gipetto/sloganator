import { useState, useEffect, useRef } from "react"
import type { FormEvent, ChangeEvent } from "react"
import PreviousSlogans from "./PreviousSlogans"
import EditorFooter from "./EditorFooter"
import EditorForm from "./EditorForm"
import { useSlogansContext } from "../contexts/SlogansContext"
import Error from "../../../src/components/Core/Alert"
import "../styles/EditorContainer.scss"

const initClickOutListener = (
  widgetRef: React.RefObject<HTMLDivElement>,
  setEditing: (b: boolean) => void
) => {
  useEffect(() => {
    const handleClick = (event: MouseEvent) => {
      if (
        widgetRef?.current &&
        !widgetRef.current.contains(event.target as HTMLElement)
      ) {
        setEditing(false)
      }
    }

    document.addEventListener("mousedown", handleClick)

    return () => {
      document.removeEventListener("mousedown", handleClick)
    }
  }, [widgetRef])
}

const EditorContainer = () => {
  const [value, setValue] = useState("")
  const {
    state: { slogans, submitError },
    actions: { setEditing, addSlogan, reset },
  } = useSlogansContext()

  const widgetRef = useRef(null)
  initClickOutListener(widgetRef, setEditing)

  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    setValue(e.target.value)
  }

  const handleSubmit = (e: FormEvent) => {
    e.stopPropagation()
    e.preventDefault()
    addSlogan(value)
  }

  const copySlogan = (slogan: string) => {
    setValue(slogan)
  }

  return (
    <div className="slogan-editor" ref={widgetRef}>
      {submitError && (
        <Error
          type="error"
          error={submitError}
          dismissable={true}
          onDismiss={() => {
            reset()
          }}
        />
      )}
      {!submitError && (
        <>
          <EditorForm
            value={value}
            handleSubmit={handleSubmit}
            handleChange={handleChange}
            cancelEditing={() => setEditing(false)}
          />
          <PreviousSlogans slogans={slogans} copySlogan={copySlogan} />
        </>
      )}
      <EditorFooter />
    </div>
  )
}

export default EditorContainer
