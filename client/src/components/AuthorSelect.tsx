import axios from "axios"
import { useEffect, useState } from "react"
import { AuthorsList } from "../types"


function AuthorSelect() {
    const url = new URL(window.location.href)
    const selectedAuthor = parseInt(url.searchParams.get("author") as string, 10).toString()

    const [authors, setAuthors] = useState<AuthorsList>([])
    const host = "tower.wookiee.internal:8080"
    const path = "mies/sloganator/v1/authors"

    useEffect(() => {
        let url = `http://${host}/${path}`

        axios.get(url)
            .then(response => response.data)
            .then((authors: AuthorsList) => authors.sort((a, b) => {
                return a.usernames[0].localeCompare(b.usernames[0])
            }))
            .then(authors => setAuthors(authors))
    }, [])

    if (!authors.length) {
        return null
    }

    return (
        <select name="author" id="author" defaultValue={selectedAuthor}>
            <option className="placeholder" value="">** Select Author **</option>
            {authors.map((author) => {
                return (
                    <option 
                        key={author.userid} 
                        value={author.userid}>
                            {author.usernames.join(", ")}
                    </option>
                )
            })}
        </select>
    )
}

export default AuthorSelect
