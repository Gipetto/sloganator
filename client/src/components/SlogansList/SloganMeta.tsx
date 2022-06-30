import type { Slogan } from "../../types"
import { LayoutRow } from "../Core/Layout"
import UserLocaleDate from "../Core/UserLocaleDate"

const SloganMeta = ({ slogan }: { slogan: Slogan }) => {
  const userProfile = `${window.location.origin}/mies/user-${slogan.userid}.html`

  return (
    <figcaption>
      <LayoutRow as="cite" justifyContent="space-between">
        <a
          className="user-name"
          target="_blank"
          rel="noreferrer"
          href={userProfile}
        >
          {slogan.username}
        </a>
        <UserLocaleDate timestamp={slogan.timestamp} />
      </LayoutRow>
    </figcaption>
  )
}

export default SloganMeta
