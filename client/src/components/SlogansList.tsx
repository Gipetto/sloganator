import React from "react"
import AuthorFilter from "./AuthorFilter"
import SlogansListItem from "./SlogansListItem"
import {SloganResponse} from "../types"
import UserContext from "../contexts/UserContext"
import sloganatorClient from "../clients/sloganator"
import "../styles/browse.css"


const defaultState = {
    selectedAuthor: undefined,
    slogans: [],
    meta: {
        filter: [],
        page: 0,
        pageSize: 0,
        previousPage: 0,
        nextPage: 0,
        results: 0
    }
}

type SlogansListState = {
    selectedAuthor: number|undefined
} & SloganResponse

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
        let queryParams = new URLSearchParams()
        if (this.state.selectedAuthor) {
            queryParams.set('author', this.state.selectedAuthor.toString())
        }

        sloganatorClient.get(`${this.path}?` + queryParams)
            .then((response) => this.setSlogans(response.data))
    }

    componentDidUpdate(_: any, prevState: SlogansListState) {
        if (this.state.selectedAuthor != prevState.selectedAuthor) {
            let queryParams = new URLSearchParams()
            if (this.state.selectedAuthor) {
                queryParams.set('author', this.state.selectedAuthor.toString())
            }

            sloganatorClient.get(`${this.path}?` + queryParams)
                .then((response) => this.setSlogans(response.data))
        }
    }

    setSlogans(sloganResponse: SloganResponse) {
        this.setState((state) => ({
            ...state,
            ...sloganResponse
        }))
    }

    setSelectedAuthor(selectedAuthor: number) {
        this.setState((state) => ({
            ...state,
            selectedAuthor
        }))
    }

    render() {
        const slogans = this.state.slogans
        const currentUser = this.context.currentUser
        const selectedAuthor = this.state.selectedAuthor

        return (
            <div>
                <AuthorFilter 
                    selectedAuthor={selectedAuthor}
                    setSelectedAuthor={this.setSelectedAuthor.bind(this)} 
                />
                <div id="slogans">
                    <ul>
                    {slogans.map((slogan) => {
                        return <SlogansListItem 
                            slogan={slogan}
                            currentUser={currentUser}
                            key={slogan.rowid} 
                        />
                    })}
                    </ul>
                </div>
            </div>
        )
    }
}

export default SlogansList
