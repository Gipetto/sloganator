import axios from "axios"
import type { AxiosError } from "axios"
import axiosRetry from "axios-retry"
// eslint-disable-next-line import/no-unresolved
// import bofhUrl from "../resources/bofh.json?url"
import type {
  AuthorsList,
  ErrorResponse,
  GetSloganParams,
  Slogan,
  SlogansResponse,
  User,
} from "../types"

let apiHost = window.location.origin

if (import.meta.env.DEV) {
  apiHost = `http://${import.meta.env.VITE_API_HOST}`
}

const BASE_PATH = "/mies/sloganator"
const SLOGANS_PATH = `${BASE_PATH}/v1/slogans`
const LATEST_SLOGAN_PATH = `${BASE_PATH}/v1/slogans/latest`
const AUTHORS_PATH = `${BASE_PATH}/v1/authors`
const USER_PATH = `${BASE_PATH}/v1/user`
// const BOFH_PATH = `${BASE_PATH}${bofhUrl}`

axiosRetry(axios, { retryDelay: axiosRetry.exponentialDelay })
const sloganatorClient = axios.create({
  baseURL: apiHost,
})

const handleException = (error: Error | AxiosError): ErrorResponse => {
  // @TODO: fix me - Error log for now in lieu of proper error reporting
  // eslint-disable-next-line no-console
  console.log(error)

  if (axios.isAxiosError(error)) {
    if (error.response) {
      // The request was made and the server
      // responded with a status code that
      // falls out of the range of 2xx

      const { data } = error.response
      return data.code < 500
        ? data
        : {
            code: data.code,
            message: "Internal Server Error",
          }
    }
    if (error.request) {
      // The request was made but no response
      // was received `error.request` is an
      // instance of XMLHttpRequest in the browser
      // and an instance of http.ClientRequest in node.js
      return {
        code: 503,
        message: "Service Unavailable",
      }
    }
  }

  // Something happened in setting up the
  // request that triggered an Error
  return {
    code: 0,
    message: "Internal Client Error",
  }
}

const fetchSlogans = (
  params: GetSloganParams,
  handleResponse: (s: SlogansResponse) => void,
  handleError?: (e?: ErrorResponse) => void
): void => {
  const queryParams = new URLSearchParams()

  if (params.author) {
    queryParams.set("author", params.author.toString())
  }

  if (params.page) {
    queryParams.set("page", params.page.toString())
  }

  if (params.pageSize) {
    queryParams.set("pageSize", params.pageSize.toString())
  }

  sloganatorClient
    .get(`${SLOGANS_PATH}?${queryParams}`)
    .then((response) => response.data as SlogansResponse)
    .then((slogans: SlogansResponse) => {
      handleResponse(slogans)
    })
    .catch((error: Error | AxiosError) => {
      const errorResponse = handleException(error)

      if (handleError) {
        handleError(errorResponse)
      }
    })
}

const fetchAuthors = (
  handleResponse: (a: AuthorsList) => void,
  handleError?: (e?: ErrorResponse) => void
): void => {
  sloganatorClient
    .get(AUTHORS_PATH)
    .then((response) => response.data)
    .then((authors: AuthorsList) => {
      handleResponse(authors)
    })
    .catch((error: Error | AxiosError) => {
      const errorResponse = handleException(error)

      if (handleError) {
        handleError(errorResponse)
      }
    })
}

const fetchUser = (
  handleResponse: (u: User) => void,
  handleError?: (e?: ErrorResponse) => void
): void => {
  sloganatorClient
    .get(USER_PATH)
    .then((response) => response.data)
    .then((u: User) => handleResponse(u))
    .catch((error: Error | AxiosError) => {
      const errorResponse = handleException(error)

      if (handleError) {
        handleError(errorResponse)
      }
    })
}

const fetchLatest = (
  handleResponse: (a: Slogan) => void,
  handleError: (a?: ErrorResponse) => void
): void => {
  sloganatorClient
    .get(LATEST_SLOGAN_PATH)
    .then((response) => response.data)
    .then((s: Slogan) => handleResponse(s))
    .catch((error: Error | AxiosError) => {
      const errorResponse = handleException(error)
      if (handleError) {
        handleError(errorResponse)
      }
    })
}

const submitSlogan = (
  data: Record<string, string>,
  handleResponse: (a: Slogan) => void,
  handleError: (a?: ErrorResponse) => void
): void => {
  sloganatorClient
    .post(SLOGANS_PATH, data)
    .then((response) => response.data)
    .then((s: Slogan) => handleResponse(s))
    .catch((error: Error | AxiosError) => {
      const errorResponse = handleException(error)
      if (handleError) {
        handleError(errorResponse)
      }
    })
}

export default sloganatorClient
export { fetchSlogans, fetchLatest, submitSlogan, fetchAuthors, fetchUser }
