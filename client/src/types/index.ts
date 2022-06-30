/**
 * Any type that is shared across
 * multiple components.
 *
 * Non-shared types may stay within
 * the files in which they're used.
 */

type Slogan = {
  rowid: number
  slogan: string
  timestamp: number
  userid: number
  username: string
}

type ResponseMeta = {
  filter: string[]
  page: number
  pageSize: number
  previousPage: number
  nextPage: number
  results: number
}

type SloganResponse = {
  slogans: Slogan[]
  meta: ResponseMeta
}

interface HTTPError {
  code: number
  message: string
}

type ErrorResponse = HTTPError

type Author = {
  userid: number
  usernames: string[]
}

type AuthorsList = Author[]

type User = {
  userId: number
  userName: string
}

interface CurrentUserContext {
  loading: boolean
  currentUser: User
  error?: ErrorResponse | undefined
}

type GetSloganParams = {
  page?: number
  author?: number
}

type SelectedAuthor = number | undefined

// Add items to exports in alphabetical order!
export type {
  Author,
  AuthorsList,
  CurrentUserContext,
  HTTPError,
  ErrorResponse,
  GetSloganParams,
  ResponseMeta,
  SelectedAuthor,
  Slogan,
  SloganResponse,
  User
}
