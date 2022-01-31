import AuthorSelect from "./AuthorSelect"


function AuthorFilter() {
    const url = new URL(window.location.href)

    return (
        <section id="filter">
            <form>
                <label htmlFor="author">Author: </label>
                <div className="select">
                    <AuthorSelect/>
                </div>
                <button type="submit">Filter</button> <a href={url.pathname}>reset</a>
            </form>
        </section>
    )
}

export default AuthorFilter
