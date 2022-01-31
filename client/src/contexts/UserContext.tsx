import React from "react"
import axios from "axios"
import { CurrentUserContext, User } from "../types"
import { createContext } from "react"

const DEFAULT_USER_ID = 0
const DEFAULT_USER_NAME = "Treefort Lover"

const defaultUser: User = {
    userId: DEFAULT_USER_ID,
    userName: DEFAULT_USER_NAME
}

const UserContext = createContext<CurrentUserContext>({
    loading: true,
    currentUser: defaultUser,
    selectedUser: DEFAULT_USER_ID,
    updateSelectedUser: () => {}
})

const UserConsumer = UserContext.Consumer

class UserProvider extends React.Component {
    state: {
        loading: true,
        currentUser: User,
        selectedUser: number,
        updateSelectedUser: Function
    }

    constructor(props: any) {
        super(props)

        const urlParams = new URLSearchParams(window.location.search)

        this.state = {
            loading: true,
            currentUser: defaultUser,
            selectedUser: parseInt(urlParams.get('author') || `${DEFAULT_USER_ID}`, 10),
            updateSelectedUser: this.setSelectedUser
        }
    }

    componentDidMount() {
        const host = "tower.wookiee.internal:8080"
        const path = "mies/sloganator/v1/user"

        axios.get(`http://${host}/${path}`)
            .then((response) => this.setCurrentUser(response.data))
            .then((_) => this.setLoading(false))
    }
    
    setCurrentUser = (currentUser: User) => {
        this.setState((state) => ({ 
            ...state,
            currentUser
        }))
    }

    setSelectedUser = (selectedUser: number) => {
        this.setState((state) => ({
            ...state,
            selectedUser
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
