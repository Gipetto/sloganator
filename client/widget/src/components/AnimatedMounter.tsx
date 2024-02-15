import React, { useEffect, useState } from "react"

interface AnimatedMounterProps {
  show: boolean
  positionAbsolute: boolean
  children: React.ReactNode
}

const AnimatedMounter = ({
  show,
  positionAbsolute,
  children,
}: AnimatedMounterProps) => {
  const [shouldRender, setRender] = useState(show)

  useEffect(() => {
    console.log(show)
    if (show) {
      setRender(true)
    }
  }, [show])

  const onAnimationEnd = () => {
    if (!show) {
      setRender(false)
    }
  }

  let style: Record<string, string> = {
    animation: `${show ? "appear" : "disappear"} 0.1s`,
  }

  if (positionAbsolute) {
    style = {
      ...style,
      position: "absolute",
      width: "100%",
    }
  }

  return shouldRender ? (
    <div style={style} onAnimationEnd={onAnimationEnd}>
      {children}
    </div>
  ) : null
}

export default AnimatedMounter
