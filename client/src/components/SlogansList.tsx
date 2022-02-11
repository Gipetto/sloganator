import React from "react"
import AuthorFilter from "./AuthorFilter"
import SlogansListPage from "./SlogansListPage"
import {SloganResponse} from "../types"
import UserContext from "../contexts/UserContext"
import { getSlogans } from "../clients/sloganator"
import LoadButton from "./LoadButton"
import "../styles/browse.css"


const defaultState = {
    selectedAuthor: undefined,
    currentPage: 1,
    isLastPage: false,
    loading: true,
    responses: []
}

type SlogansListState = {
    selectedAuthor: number|undefined,
    currentPage: number,
    isLastPage: boolean,
    loading: boolean,
    responses: SloganResponse[]
}

class SlogansList extends React.Component<any, SlogansListState> {
    static contextType = UserContext

    path = "/mies/sloganator/v1/slogans"
    
    constructor(props: any) {
        super(props)
        
        let initialState:any = {}

        const pageQuery = new URLSearchParams(window.location.search)
        if(pageQuery.has('author')) {
            initialState.selectedAuthor = parseInt(pageQuery.get('author') || "", 10)
        }
        
        this.state = {
            ...defaultState,
            ...initialState
        }
    }

    componentDidMount() {
        getSlogans({
            author: this.state.selectedAuthor
        }).then((response) => {
            if (response.hasOwnProperty("slogans")) {
                this.setSlogans(response as SloganResponse)
            } else {
                // @TODO - set loading error 
            }
        })
    }

    componentDidUpdate(_: any, prevState: SlogansListState) {
        if (this.state.selectedAuthor !== prevState.selectedAuthor) {
            getSlogans({
                author: this.state.selectedAuthor
            }).then((response) => {
                if (response.hasOwnProperty("slogans")) {
                    this.setSlogans(response as SloganResponse)
                } else {
                    // @TODO - set loading error 
                }
            })
        }
    }

    setLoading(isLoading: boolean = true) {
        this.setState((state) => ({
            ...state,
            loading: isLoading
        }))
    }

    /**
     * Start a fresh list of slogan responses
     * @param sloganResponse
     */
    setSlogans(sloganResponse: SloganResponse) {
        this.setState((state) => ({
            ...state,
            loading: false,
            currentPage: sloganResponse.meta.page,
            isLastPage: typeof sloganResponse.meta.nextPage !== "number",
            responses: [
                sloganResponse
            ]
        }))
    }

    /**
     * Append a slogan response to the list of responses
     * @param sloganResponse
     */
    updateSlogans(sloganResponse: SloganResponse) {
        this.setState((state) => ({
            ...state,
            loading: false,
            currentPage: sloganResponse.meta.page,
            isLastPage: typeof sloganResponse.meta.nextPage !== "number",
            responses: [
                ...state.responses,
                sloganResponse
            ]
        }))        
    }

    setSelectedAuthor(selectedAuthor: number) {
        this.setState((state) => ({
            ...state,
            selectedAuthor
        }))
    }

    nextPage() {
        this.setLoading()
        const nextPage = this.state.responses[this.state.responses.length - 1].meta.page + 1
        getSlogans({ 
            author: this.state.selectedAuthor,
            page: nextPage
        }).then((response) => {
            if (response.hasOwnProperty("slogans")) {
                this.updateSlogans(response as SloganResponse)
            } else {
                // @TODO - set loading error 
            }
        })
    }

    render() {
        const sloganResponses = this.state.responses
        const currentUser = this.context.currentUser
        const selectedAuthor = this.state.selectedAuthor
        const isLoading = this.state.loading
        const isLastPage = this.state.isLastPage
        let nextPage = this.state.currentPage

        return (
            <section>
                <AuthorFilter 
                    selectedAuthor={selectedAuthor}
                    setSelectedAuthor={this.setSelectedAuthor.bind(this)} 
                />
                <div id="slogans">
                    <ul>
                    {sloganResponses.map((response) => {
                        nextPage = response.meta.nextPage
                        return <SlogansListPage 
                            response={response}
                            currentUser={currentUser}
                            key={`page-${response.meta.page}`}
                        />
                    })}
                    </ul>
                </div>
                <div id="paginator">
                    {!isLastPage && <LoadButton 
                        clickHandler={this.nextPage.bind(this)} 
                        page={nextPage} 
                        loading={isLoading}
                    />}
                    {isLastPage && <b>!!! There's no more to show… </b>}
                    <a href="#top">back to top</a>
                </div>
            </section>
        )
    }
}

export default SlogansList
