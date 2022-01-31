import React from "react"
import { CurrentUserContext, User } from "../types"
import { createContext } from "react"
import sloganatorClient from "../clients/sloganator"

const DEFAULT_USER_ID = 0
const DEFAULT_USER_NAME = "Treefort Lover"

const defaultUser: User = {
    userId: DEFAULT_USER_ID,
    userName: DEFAULT_USER_NAME
}

const UserContext = createContext<CurrentUserContext>({
    loading: true,
    currentUser: defaultUser
})

const UserConsumer = UserContext.Consumer

class UserProvider extends React.Component {
    state: {
        loading: true,
        currentUser: User,
    }

    constructor(props: any) {
        super(props)

        this.state = {
            loading: true,
            currentUser: defaultUser
        }
    }

    componentDidMount() {
        sloganatorClient.get("/mies/sloganator/v1/user")
            .then((response) => this.setCurrentUser(response.data))
            .then((_) => this.setLoading(false))
    }
    
    setCurrentUser = (currentUser: User) => {
        this.setState((state) => ({ 
            ...state,
            currentUser
        }))
    }

    setLoading = (loading: boolean) => {
        this.setState((state) => ({
            ...state,
            loading
        }))
    }

    render() {
        const { children } = this.props

        return (
            <UserContext.Provider value={this.state}>
                {children}
            </UserContext.Provider>
        )
    }
}

export default UserContext
export { defaultUser, UserConsumer, UserProvider }
