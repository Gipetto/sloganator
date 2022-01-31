import React from "react"
import axios from "axios"
import AuthorFilter from "./AuthorFilter"
import SlogansListItem from "./SlogansListItem"
import {SloganResponse} from "../types"
import UserContext from "../contexts/UserContext"
import "../styles/browse.css"


const defaultState = {
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

class SlogansList extends React.Component {
    static contextType = UserContext

    state: SloganResponse

    host = "tower.wookiee.internal:8080"
    path = "mies/sloganator/v1/slogans"
    
    constructor(props: any) {
        super(props)
        this.state = defaultState
    }

    componentDidMount() {
        let queryParams = new URLSearchParams()
        if (this.context.selectedUser) {
            queryParams.set('author', this.context.selectedUser)
        }

        axios.get(`http://${this.host}/${this.path}?` + queryParams)
            .then((response) => this.setSlogans(response.data))
    }

    setSlogans(sloganResponse: SloganResponse) {
        this.setState((state) => ({
            ...state,
            ...sloganResponse
        }))
    }

    render() {
        const slogans = this.state.slogans
        const currentUser = this.context.currentUser

        return (
            <div>
                <AuthorFilter />
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
