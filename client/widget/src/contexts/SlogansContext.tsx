import React, { createContext, ReactNode, useEffect, useState } from "react"
import { fetchSlogans, submitSlogan } from "../../../src/clients/sloganator"
import type {
  ErrorResponse,
  HTTPError,
  Slogan,
  SlogansResponse,
} from "../../../src/types"

interface CurrentSlogansContextState {
  loading: boolean
  submitting: boolean
  editing: boolean
  slogans: Slogan[]
  loadError?: ErrorResponse
  submitError?: ErrorResponse
}

interface CurrentSlogansContextActions {
  setEditing: (a: boolean) => void
  addSlogan: (s: string) => void
  reset: () => void
}

interface CurrentSlogansContext {
  state: CurrentSlogansContextState
  actions: CurrentSlogansContextActions
}

const SlogansContext = createContext<CurrentSlogansContext>({
  state: {
    loading: false,
    submitting: false,
    editing: false,
    slogans: [],
  },
  actions: {
    setEditing: (): void => {},
    addSlogan: (): void => {},
    reset: (): void => {},
  },
})

const SlogansContextProvider = ({ children }: { children: ReactNode }) => {
  const [isLoading, setLoading] = useState<boolean>(true)
  const [isSubmitting, setSubmitting] = useState<boolean>(true)
  const [loadError, setLoadError] = useState<HTTPError | undefined>(undefined)
  const [submitError, setSubmitError] = useState<HTTPError | undefined>(
    undefined
  )
  const [slogans, setSlogans] = useState<Slogan[]>([])
  const [editing, setEditing] = useState<boolean>(false)

  const reset = () => {
    setEditing(false)
    setLoading(false)
    setLoadError(undefined)
    setSubmitError(undefined)
    setSlogans([])
    loadSlogans()
  }

  const addSlogan = (sloganText: string) => {
    setSubmitting(true)
    submitSlogan(
      { slogan: sloganText },
      (response: Slogan) => {
        setSubmitting(false)
        setEditing(false)
        loadSlogans()
      },
      (e) => {
        setSubmitting(false)
        setSubmitError(e)
      }
    )
  }

  const loadSlogans = () => {
    setLoading(true)
    fetchSlogans(
      { pageSize: 10 },
      (response: SlogansResponse) => {
        setLoading(false)
        setLoadError(undefined)
        setSlogans(response.slogans)
      },
      (e) => {
        setLoading(false)
        setLoadError(e)
      }
    )
  }

  useEffect(() => {
    loadSlogans()
  }, [])

  const isLoadError = loadError !== undefined

  const value = {
    state: {
      loading: isLoading,
      submitting: isSubmitting,
      loadError: loadError,
      submitError: submitError,
      editing: editing,
      slogans: slogans,
    },
    actions: {
      setEditing,
      addSlogan,
      reset,
    },
  }

  return (
    <>
      {isLoading && <p>Loading...</p>}
      {!isLoading && isLoadError && (
        <p>
          Failed to load slogan! Server says: ({loadError.code})
          {loadError.message}
        </p>
      )}
      {!isLoading && !isLoadError && (
        <SlogansContext.Provider value={value}>
          {children}
        </SlogansContext.Provider>
      )}
    </>
  )
}

const useSlogansContext = () => React.useContext(SlogansContext)

export default SlogansContext
export { SlogansContextProvider, useSlogansContext }
