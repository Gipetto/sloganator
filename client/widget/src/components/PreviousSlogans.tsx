import { Slogan } from "../../../app/src/types"
import "../styles/PreviousSlogans.scss"
import { ListPanel, ListPanelItem } from "./EightDotOne"

interface PreviousSlogansProps {
  slogans: Slogan[]
  copySlogan: (s: string) => void
}

const PreviousSlogans = ({ slogans, copySlogan }: PreviousSlogansProps) => {
  const previousSlogansLen = 10

  return (
    <ListPanel>
      {slogans.slice(0, previousSlogansLen).map((slogan) => (
        <ListPanelItem key={`${slogan.timestamp}`}>
          <button onClick={() => copySlogan(slogan.slogan)}>
            <span className="slogan">{slogan.slogan}</span>
            <span className="author">{slogan.username}</span>
          </button>
        </ListPanelItem>
      ))}
    </ListPanel>
  )
}

export default PreviousSlogans
