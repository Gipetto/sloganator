import {Slogan, User} from "../types"

type ListItemProps = { 
    slogan: Slogan, 
    currentUser: User 
}

function SlogansListItem(props: ListItemProps) {
    const {slogan, currentUser} = props

    const lang = navigator.language
    const localDateOptions: Intl.DateTimeFormatOptions = {
        hour: "numeric",
        minute: "numeric",
        day: "numeric",
        month: "short",
        year: "numeric",
        hour12: false,
        timeZoneName: "short"
    }
    const date = new Date(slogan.timestamp * 1000)
    const dateString = Intl.DateTimeFormat(lang, localDateOptions).format(date)

    const userProfile = `${window.location.origin}/mies/user-${slogan.userid}.html`

    let figClass = ""
    if (slogan.userid === currentUser.userId) {
        figClass += "current-user-author"
    }

    return (
        <li>
            <figure className={figClass}>
                <blockquote>
                    <p>{slogan.slogan}</p>
                </blockquote>
                <figcaption>
                    <cite>
                        <span className="timestamp">{dateString}</span>
                        <a className="user-name" target="_blank" rel="noreferrer" href={userProfile}>{slogan.username}</a>
                    </cite>
                </figcaption>
            </figure>
        </li>
    )
}

export default SlogansListItem
