import { ChangeEvent, useEffect, useRef } from "react"

interface ExpandingTextAreaProps {
  autoComplete: "off" | "on"
  spellCheck: "false" | "true"
  maxLength: number
  name: string
  onChange: (e: ChangeEvent<HTMLTextAreaElement>) => void
  placeholder: string
  value: string
}

const ExpandingTextArea = (props: ExpandingTextAreaProps) => {
  const taRef = useRef<any>()

  useEffect(() => {
    taRef.current.style.height = "0" // this can also be "auto" if "0" is too jumpy
    taRef.current.style.height = `${taRef.current.scrollHeight}px`
  }, [props.value])

  return <textarea {...props} ref={taRef} rows={1} />
}

export default ExpandingTextArea
