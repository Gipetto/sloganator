import type { Slogan, User } from "../../types"
import SloganBody from "./SloganBody"
import SloganMeta from "./SloganMeta"

interface ListItemProps {
  slogan: Slogan
  currentUser: User
}

const SlogansListItem = (props: ListItemProps) => {
  const { slogan, currentUser } = props

  let figClass = ""
  if (slogan.userid === currentUser.userId) {
    figClass += "current-user-author"
  }

  return (
    <li>
      <figure className={figClass}>
        <SloganBody slogan={slogan} />
        <SloganMeta slogan={slogan} />
      </figure>
    </li>
  )
}

export default SlogansListItem
