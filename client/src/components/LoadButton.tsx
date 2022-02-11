type LoadProps = {
    page: number,
    loading: boolean,
    clickHandler: Function
}

function LoadButton(props:LoadProps) {
    const page = props.page
    const clickHandler = props.clickHandler
    const isLoading = props.loading
    const buttonText = isLoading ? "Loading Page" : "Load Page"

    return(
        <button 
            className="loader" 
            type="button" 
            onClick={(e) => {
                    if (isLoading) {
                        return false;
                    }
                    e.preventDefault()
                    clickHandler()
                }}
            >
            {buttonText} <span className="page">{page}</span>
        </button>
    )
}

export default LoadButton
