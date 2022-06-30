import { ButtonHTMLAttributes, DetailedHTMLProps } from "react"
import "../../styles/Button.css"

interface ButtonProps
  extends DetailedHTMLProps<ButtonHTMLAttributes<HTMLButtonElement>, HTMLButtonElement> {
  link?: boolean
}

const Button = (props: ButtonProps) => {
  const {
    children, type, link, className, ...rest
  } = props

  const classes = [className]
  if (link) {
    classes.push("link")
  }

  return (
    <button
      type={type}
      className={classes.filter((i) => i).join(" ")}
      {...rest}
    >
      {children}

    </button>
  )
}

export default Button
