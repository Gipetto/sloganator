import { useEffect, useState } from "react"
import AuthorFilter from "../AuthorFilter/AuthorFilter"
import SlogansListPage from "./SlogansListPage"
import { ErrorResponse, SlogansResponse } from "../../types"
import { useUserContext } from "../../contexts/UserContext"
import { fetchSlogans } from "../../clients/sloganator"
import { useAuthorContext } from "../../contexts/AuthorContext"
import Paginator from "./Paginator"
import type { GetSloganParams } from "../../types"
import { LayoutCol } from "../Core/Layout"
import Error from "../Core/Alert"
import "../../styles/SlogansList.scss"

const defaultState = {
  selectedAuthor: undefined,
  currentPage: 1,
  isLastPage: false,
  loading: true,
  error: undefined,
  responses: [],
}

interface SlogansListState {
  selectedAuthor: number | undefined
  currentPage: number
  isLastPage: boolean
  loading: boolean
  error: ErrorResponse | undefined
  responses: SlogansResponse[]
}

const SlogansList = () => {
  const userContext = useUserContext()
  const authorContext = useAuthorContext()

  const [state, setState] = useState<SlogansListState>({
    ...defaultState,
    selectedAuthor: authorContext.selectedAuthor,
  })

  const loadPage = (params: GetSloganParams, replaceSlogans = false) => {
    setState((prevState) => ({
      ...prevState,
      error: undefined,
    }))

    fetchSlogans(
      params,
      (response) => {
        setState((prevState) => {
          const responses = replaceSlogans
            ? [response]
            : [...prevState.responses, response]

          return {
            ...prevState,
            loading: false,
            currentPage: response.meta.page,
            isLastPage: typeof response.meta.nextPage !== "number",
            responses,
          }
        })
      },
      (e) => {
        setState((prevState) => {
          return {
            ...prevState,
            loading: false,
            error: {
              code: e?.code || 0,
              message:
                e?.message ||
                "An expected error has occurred. The server doesn't like you.",
            },
          }
        })
      }
    )
  }

  const loadNextPage = () => {
    setState((prevState) => ({
      ...prevState,
      loading: true,
    }))
    const nextPage = state.responses[state.responses.length - 1].meta.page + 1

    loadPage({
      page: nextPage,
      author: state.selectedAuthor,
    })
  }

  /**
   * Maybe not the best invocation in the world.
   * This fires when:
   * - UserContext initializes the selectedAuthor at page load
   * - selectedAuthor changes
   *
   * The first reason there feels... brittle? Or is it correct
   * because we should only fire the slogan load when we know
   * the true state of the selectedAuthor?
   */
  useEffect(() => {
    setState((prevState) => ({
      ...prevState,
      selectedAuthor: authorContext.selectedAuthor,
    }))

    loadPage(
      {
        author: authorContext.selectedAuthor,
      },
      true
    )
  }, [authorContext.selectedAuthor])

  const isLoading = state.loading
  const { isLastPage, error } = state
  let nextPage = state.currentPage

  return (
    <LayoutCol as="section">
      <AuthorFilter />
      <div id="slogans">
        <ul>
          {state.responses.map((response) => {
            nextPage = response.meta.nextPage
            return (
              <SlogansListPage
                response={response}
                currentUser={userContext.currentUser}
                key={`page-${response.meta.page}`}
              />
            )
          })}
          {error && <Error error={error} iconSize="large" />}
        </ul>
      </div>
      <Paginator
        isLastPage={isLastPage}
        page={nextPage}
        isLoading={isLoading}
        onClick={loadNextPage}
      />
    </LayoutCol>
  )
}

export default SlogansList
