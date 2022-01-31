import { AuthorsList } from "../types"

type AuthorSelectProps = {
    authors: AuthorsList,
    selectedAuthor: number|undefined,
    updateSelectedAuthor: Function
}

function AuthorSelect(props: AuthorSelectProps) {
    const authors = props.authors
    if (!authors.length) {
        return null
    }

    const selectedAuthor = props.selectedAuthor
    const handleChange = props.updateSelectedAuthor

    return (
        <select 
            name="author" 
            id="author" 
            defaultValue={selectedAuthor}
            onChange={(e) => handleChange(e.currentTarget.value)}
        >
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
