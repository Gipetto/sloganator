import { Slogan } from "../../../app/src/types"
import "../styles/CurrentSlogan.scss"

interface CurrentSloganProps {
  slogan: Slogan
  setEditing: (a: boolean) => void
}

const CurrentSlogan = ({ slogan, setEditing }: CurrentSloganProps) => {
  return (
    <div>
      <button onClick={() => setEditing(true)}>{slogan?.slogan}</button>
    </div>
  )
}

export default CurrentSlogan
