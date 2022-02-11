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
            .catch((error) => {
                if (error.response) {
                    // The request was made and the server responded with a status code
                    // that falls out of the range of 2xx
                    console.log(error.response.data);
                    console.log(error.response.status);
                    console.log(error.response.headers);
                  } else if (error.request) {
                    // The request was made but no response was received
                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                    // http.ClientRequest in node.js
                    console.log(error.request);
                  } else {
                    // Something happened in setting up the request that triggered an Error
                    console.log('Error', error.message);
                  }
                  console.log(error.config);
            })
            .finally(() => this.setLoading(false))
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

        if (this.state.loading) {
            return (
                <p>Loading...</p>
            )
        }

        return (
            <UserContext.Provider value={this.state}>
                {children}
            </UserContext.Provider>
        )
    }
}

export default UserContext
export { defaultUser, UserConsumer, UserProvider }
