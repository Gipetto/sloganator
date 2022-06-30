import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import {
  faBomb,
  faPoop,
  faBullhorn,
  faMedal,
  IconDefinition
} from "@fortawesome/free-solid-svg-icons"
import type { ErrorResponse } from "../../types"
import { LayoutRow } from "./Layout"
import "../../styles/Alert.scss"

interface ErrorProps {
  error?: ErrorResponse;
  message?: string;
  type?: "notice" | "warning" | "error" | undefined;
}

const statusClassFromHTTPCode = (code: number): string => {
  switch (true) {
    case code >= 500 || code === 0:
      return "error"
    case code >= 400 && code < 500:
      return "warning"
    case code >= 200 && code < 300:
      return "success"
    default:
      return "info"
  }
}

const icons: {
  [k: string]: IconDefinition;
} = {
  error: faBomb,
  warning: faPoop,
  info: faBullhorn,
  success: faMedal
}

const Error = (props: ErrorProps) => {
  const { error, message, type } = props

  let statusClass = type || "info"
  let statusMessage = message

  if (error) {
    statusClass = error.code
      ? statusClassFromHTTPCode(error.code)
      : statusClass
    statusMessage = error.message || statusMessage
  }

  return (
    <LayoutRow as="div" className={`alert ${statusClass}`}>
      <FontAwesomeIcon
        className="alert-icon"
        size="5x"
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
