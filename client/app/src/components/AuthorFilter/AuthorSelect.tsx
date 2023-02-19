import { AuthorsList, SelectedAuthor } from "../../types"
import StyledSelect from "../Core/StyledSelect"

interface AuthorSelectProps {
  authors: AuthorsList
  selectedAuthor: number | undefined
  updateSelectedAuthor: (i: SelectedAuthor) => void
}

function AuthorSelect(props: AuthorSelectProps) {
  const { authors } = props
  if (!authors.length) {
    return null
  }

  const { selectedAuthor } = props
  const handleChange = props.updateSelectedAuthor

  return (
    <StyledSelect
      name="author"
      defaultValue={selectedAuthor}
      onChange={(i: number) => handleChange(i)}
    >
      <option className="placeholder" value="">
        ** Select Author **
      </option>
      {authors.map((author) => (
        <option key={author.userid} value={author.userid}>
          {author.usernames.join(", ")}
        </option>
      ))}
    </StyledSelect>
  )
}

export default AuthorSelect
