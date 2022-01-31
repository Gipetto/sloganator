export type Slogan = {
    rowid: number,
    slogan: string,
    timestamp: number,
    userid: number,
    username: string
}

export type ResponseMeta = {
    filter: Array<string>,
    page: number,
    pageSize: number,
    previousPage: number,
    nextPage: number,
    results: number
}

export type SloganResponse = {
    slogans: Array<Slogan>,
    meta: ResponseMeta
}

export type Author = {
    userid: number,
    usernames: Array<string>
}

export type AuthorsList = Array<Author>

export type User = {
    userId: number,
    userName: string
}

export type CurrentUserContext = {
    loading: boolean,
    currentUser: User,
    selectedUser: number,
    updateSelectedUser: Function
}