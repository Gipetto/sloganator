import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import {
  faBomb,
  faPoop,
  faBullhorn,
  faMedal,
  IconDefinition,
} from "@fortawesome/free-solid-svg-icons"
import type { ErrorResponse } from "../../types"
import { LayoutRow } from "./Layout"
import "../../styles/Alert.scss"

interface ErrorProps {
  error?: ErrorResponse
  message?: string
  type?: "notice" | "warning" | "error" | undefined
  iconSize?: "large" | "normal"
  dismissable?: boolean
  onDismiss?: () => void
}

const statusClassFromHTTPCode = (code: number): string => {
  switch (true) {
    case code >= 500 || code === 0:
      return "sl-error"
    case code >= 400 && code < 500:
      return "sl-warning"
    case code >= 200 && code < 300:
      return "sl-success"
    default:
      return "sl-info"
  }
}

const icons: {
  [k: string]: IconDefinition
} = {
  "sl-error": faBomb,
  "sl-warning": faPoop,
  "sl-info": faBullhorn,
  "sl-success": faMedal,
}

const Error = (props: ErrorProps) => {
  const {
    error,
    message,
    type,
    iconSize = "normal",
    dismissable = false,
    onDismiss = undefined,
  } = props

  let statusClass = `sl-${type}` || "sl-info"
  let statusMessage = message

  if (error) {
    statusClass = error.code ? statusClassFromHTTPCode(error.code) : statusClass
    statusMessage = `(${error.code}) ${error.message}` || statusMessage
  }

  return (
    <LayoutRow as="div" className={`sl-alert ${statusClass}`}>
      {dismissable && onDismiss && (
        <button className="alert-dismiss" onClick={onDismiss}>
          &times;
        </button>
      )}
      <FontAwesomeIcon
        className={`alert-icon-${iconSize}`}
        size={iconSize == "large" ? "5x" : "3x"}
        icon={icons[statusClass]}
      />
      <span>
        <strong>{statusMessage}</strong>
        <br />
        The administrator is being flogged as you read this.
      </span>
    </LayoutRow>
  )
}

export default Error
