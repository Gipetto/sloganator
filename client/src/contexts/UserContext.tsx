import React, {
  createContext, ReactNode, useEffect, useMemo, useState
} from "react"
import { CurrentUserContext, User } from "../types"
import { fetchUser } from "../clients/sloganator"
import Alert from "../components/Core/Alert"

const DEFAULT_USER_ID = 0
const DEFAULT_USER_NAME = "Treefort Lover"

const defaultUser: User = {
  userId: DEFAULT_USER_ID,
  userName: DEFAULT_USER_NAME
}

const UserContext = createContext<CurrentUserContext>({
  loading: true,
  currentUser: defaultUser,
  error: undefined
})

const UserContextProvider = ({ children }: {
  children: ReactNode
}) => {
  // @TODO - is useReducer more appropriate here?
  const [state, setState] = useState<CurrentUserContext>({
    loading: true,
    currentUser: defaultUser,
    error: undefined
  })

  useEffect(() => {
    fetchUser((user: User) => {
      setState((prevState) => ({
        ...prevState,
        loading: false,
        currentUser: user
      }))
    }, (e) => {
      setState((prevState) => ({
        ...prevState,
        loading: false,
        error: e
      }))
    })
  }, [])

  const value = useMemo(() => state, [state.loading])
  const isLoading = state.loading
  const isError = state.error !== undefined

  return (
    <>
      {isLoading && <p>Loading...</p>}
      {!isLoading && (
        <UserContext.Provider value={value}>
          {isError && (
            <Alert
              type="warning"
              message="An error occurred while loading your userdata."
            />
          )}
          {children}
        </UserContext.Provider>
      )}
    </>
  )
}

const useUserContext = () => React.useContext(UserContext)

export default UserContext
export { UserContextProvider, useUserContext, defaultUser }
