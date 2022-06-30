import Button from "../Core/Button"

interface LoadProps {
  page: number,
  loading: boolean,
  clickHandler: () => void
}

function LoadButton(props:LoadProps) {
  const { page } = props
  const { clickHandler } = props
  const isLoading = props.loading
  const buttonText = isLoading ? "Loading Page" : "Load Page"

  return (
    <Button
      className="loader"
      type="button"
      onClick={(e) => {
        if (isLoading) {
          return
        }

        e.preventDefault()
        clickHandler()
      }}
    >
      {buttonText}
      {" "}
      <span className="page">{page}</span>
    </Button>
  )
}

export default LoadButton
