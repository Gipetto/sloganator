import type { Slogan } from "../../types"

const SloganBody = ({ slogan }: { slogan: Slogan }) => {
  return (
    <blockquote>
      <p>{slogan.slogan}</p>
    </blockquote>
  )
}

export default SloganBody
