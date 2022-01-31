import type { AuthorsList } from "../types"
import React, { FormEvent } from "react"
import axios from "axios"
import AuthorSelect from "./AuthorSelect"

type AuthorFilterProps = {
    selectedAuthor: number|undefined,
    setSelectedAuthor: Function
}

class AuthorFilter extends React.Component<AuthorFilterProps> {
    state: {
        authors: AuthorsList,
        selectedAuthor: number|undefined
    }

    host = "tower.wookiee.internal:8080"
    path = "mies/sloganator/v1/authors"

    constructor(props: AuthorFilterProps) {
        super(props)

        this.state = {
            authors: [],
            selectedAuthor: props.selectedAuthor,
        }
    }

    componentDidMount() {
        let url = `http://${this.host}/${this.path}`

        axios.get(url)
            .then(response => response.data)
            .then((authors: AuthorsList) => authors.sort((a, b) => {
                return a.usernames[0].localeCompare(b.usernames[0])
            }))
            .then(authors => this.setState((state) => ({
                ...state,
                authors
            })))
    }

    handleChange(key: string, value: number|undefined, callback?: () => void) {
        this.setState((state) => ({
            ...state,
            [key]: value
        }), callback)
    }

    handleReset(e: FormEvent) {
        this.handleChange("selectedAuthor", undefined, () => {
            this.handleSubmit()
        })
    }

    handleSubmit(e?: FormEvent) {
        if (e) {
            e.preventDefault()
        }
        this.props.setSelectedAuthor(this.state.selectedAuthor)

        let newPath = window.location.pathname
        if (this.state.selectedAuthor) {
            newPath += "?author=" + this.state.selectedAuthor
        }

        window.history.pushState(null, "", newPath)
    }

    render() {
        const authors = this.state.authors
        const selectedAuthor = this.state.selectedAuthor

        return (
            <section id="filter">
                <form onSubmit={(e) => this.handleSubmit(e)}>
                    <label htmlFor="author">Author: </label>
                    <div className="select">
                        <AuthorSelect 
                            authors={authors}
                            selectedAuthor={selectedAuthor}
                            updateSelectedAuthor={(authorId: number) => {
                                this.handleChange("selectedAuthor", authorId)
                            }}
                        />
                    </div>
                    <button type="submit">Filter</button> 
                    <button type="reset" className="link" onClick={(e) => this.handleReset(e)}>reset</button>
                </form>
            </section>
        )
    }
}

export default AuthorFilter
