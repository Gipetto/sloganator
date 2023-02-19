import { FormEvent, useEffect, useState } from "react"
import type { AuthorsList, SelectedAuthor } from "../../types"
import { useAuthorContext } from "../../contexts/AuthorContext"
import { fetchAuthors } from "../../clients/sloganator"
import { LayoutRow } from "../Core/Layout"
import AuthorSelect from "./AuthorSelect"
import Button from "../Core/Button"
import "../../styles/AuthorFilter.css"

interface AuthorFilterState {
  authors: AuthorsList
  selectedAuthor: SelectedAuthor
}

const AuthorFilter = () => {
  const authorContext = useAuthorContext()
  const [state, setState] = useState<AuthorFilterState>({
    authors: [],
    selectedAuthor: authorContext.selectedAuthor,
  })

  useEffect(() => {
    fetchAuthors(
      (response: AuthorsList) => {
        const authors = response.sort((a, b) =>
          a.usernames[0].localeCompare(b.usernames[0])
        )
        setState((prevState) => ({ ...prevState, authors }))
      },
      (e) => {
        // @TODO handle error state
        // console.log("Author Fetch Error")
        // console.dir(e)
      }
    )
  }, [])

  const updateSelectedAuthorHandler = (selectedAuthor: SelectedAuthor) => {
    setState((prevState) => ({ ...prevState, selectedAuthor }))
  }

  const resetHandler = () => {
    updateSelectedAuthorHandler(undefined)
    authorContext.setSelectedAuthor(undefined)
  }

  const submitHandler = (e: FormEvent) => {
    e.preventDefault()
    authorContext.setSelectedAuthor(state.selectedAuthor)
  }

  return (
    <LayoutRow as="form" onSubmit={submitHandler}>
      <label htmlFor="author">Author: </label>
      <AuthorSelect
        authors={state.authors}
        selectedAuthor={authorContext.selectedAuthor}
        updateSelectedAuthor={updateSelectedAuthorHandler}
      />
      <Button type="submit">Filter</Button>
      <Button type="reset" link onClick={resetHandler}>
        reset
      </Button>
    </LayoutRow>
  )
}

export default AuthorFilter
