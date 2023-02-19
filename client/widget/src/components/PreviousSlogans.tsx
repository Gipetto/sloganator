import { Slogan } from "../../../src/types"
import "../styles/PreviousSlogans.scss"

interface PreviousSlogansProps {
  slogans: Slogan[]
  copySlogan: (s: string) => void
}

const PreviousSlogans = ({ slogans, copySlogan }: PreviousSlogansProps) => {
  const previousSlogansLen = 10

  return (
    <div className="previous-slogans">
      <p>
        <b>Recent Slogans:</b>
      </p>
      <div className="fadewrapper">
        <ol>
          {slogans.slice(0, previousSlogansLen).map((slogan) => (
            <li key={slogan.timestamp}>
              <button onClick={() => copySlogan(slogan.slogan)}>
                <span>{slogan.slogan}</span>
                <span>~ {slogan.username}</span>
              </button>
            </li>
          ))}
        </ol>
      </div>
    </div>
  )
}

export default PreviousSlogans
