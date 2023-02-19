import LoadButton from "./LoadButton"
import "../../styles/Paginator.css"

interface PaginatorProps {
  isLastPage: boolean
  isLoading: boolean
  page: number
  onClick: () => void
}

const EndOfContent = () => (
  <p>
    <b>!!! There&rsquo;s no more to show&hellip;</b>
  </p>
)

const Paginator = (props: PaginatorProps) => {
  const { isLastPage, onClick, page, isLoading } = props
  return (
    <div id="paginator">
      {!isLastPage && (
        <LoadButton clickHandler={onClick} page={page} loading={isLoading} />
      )}
      {isLastPage && <EndOfContent />}
    </div>
  )
}

export default Paginator
