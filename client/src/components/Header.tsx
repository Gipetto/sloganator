import React from "react"
import type { User } from "../types"
import UserContext from "../contexts/UserContext"

type WelcomeTextProps = {
    loading: boolean, 
    user: User
}

function WelcomeText(props: WelcomeTextProps) {
    const loading = props.loading
    if (loading) {
        return (
            <p>Hello</p>
        )
    }

    const user = props.user
    return (
        <p>Hello, <span className="current-user" data-id={user.userId}>{user.userName}</span></p>
    )
}

class Header extends React.Component {
    static contextType = UserContext

    render () {
        return (
            <header>
                <h1>Sloganator</h1>
                <WelcomeText loading={this.context.loading} user={this.context.currentUser}/>
            </header>
        )
    }
}

export default Header
