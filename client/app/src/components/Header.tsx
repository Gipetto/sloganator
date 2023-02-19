import type { User } from "../types"
import { useUserContext } from "../contexts/UserContext"
import { LayoutCell } from "./Core/Layout"
import "../styles/Header.css"

interface WelcomeTextProps {
  loading: boolean
  user: User
}

const WelcomeText = (props: WelcomeTextProps) => {
  const { loading } = props
  if (loading) {
    return <p>Hello</p>
  }

  const { user } = props
  return (
    <p>
      Hello,{" "}
      <span className="current-user" data-id={user.userId}>
        {user.userName}
      </span>
    </p>
  )
}

const Header = () => {
  const userContext = useUserContext()
  return (
    <LayoutCell id="top" as="header">
      <h1>Sloganator</h1>
      <WelcomeText
        loading={userContext.loading}
        user={userContext.currentUser}
      />
    </LayoutCell>
  )
}

export default Header
