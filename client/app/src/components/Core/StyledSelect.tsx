import { PropsWithChildren } from "react"

interface StyledSelectProps {
  name: string
  id?: string | undefined
  defaultValue: number | string | undefined
  onChange: (i: number) => void
}

const StyledSelect = ({
  name,
  id,
  children,
  defaultValue,
  onChange,
}: PropsWithChildren<StyledSelectProps>) => {
  const elementId = id || name
  return (
    <div className="select">
      <select
        name={name}
        id={elementId}
        defaultValue={defaultValue}
        onChange={(e) => onChange(parseInt(e.currentTarget.value, 10) || 0)}
      >
        {children}
      </select>
    </div>
  )
}

StyledSelect.defaultProps = {
  id: undefined,
}

export default StyledSelect
