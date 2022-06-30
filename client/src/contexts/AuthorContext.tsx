import React, { useMemo, useState, PropsWithChildren } from "react"
import type { SelectedAuthor } from "../types"

export type AuthorContextProviderProps = PropsWithChildren<Record<string, unknown>>

interface CurrentAuthorContext {
  selectedAuthor: SelectedAuthor
  setSelectedAuthor: (i: SelectedAuthor) => void
}

const defaultAuthorState: CurrentAuthorContext = {
  selectedAuthor: undefined,
  setSelectedAuthor: () => { /* do nothing */ }
}

const AuthorContext = React.createContext<CurrentAuthorContext>(
  defaultAuthorState
)

const AuthorContextProvider = ({ children }: AuthorContextProviderProps) => {
  let initialState
  const pageQuery = new URLSearchParams(window.location.search)

  if (pageQuery.has("author")) {
    initialState = parseInt(pageQuery.get("author") || "", 10)
  }

  const [selectedAuthor, setSelectedAuthor] = useState<SelectedAuthor>(initialState)

  const setSelectedAuthorHandler = (authorId: SelectedAuthor) => {
    setSelectedAuthor(authorId)

    let newPath = window.location.pathname
    if (authorId) {
      newPath += `?author=${authorId}`
    }
    window.history.pushState(null, "", newPath)
  }

  const value = useMemo(() => ({
    selectedAuthor,
    setSelectedAuthor: setSelectedAuthorHandler
  }), [selectedAuthor])

  return (
    <AuthorContext.Provider value={value}>
      {children}
    </AuthorContext.Provider>
  )
}

const useAuthorContext = () => React.useContext(AuthorContext)

export default AuthorContext
export { AuthorContextProvider, useAuthorContext }
