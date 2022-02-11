import React from "react"
import type { SloganResponse, User } from "../types"
import SlogansListItem from "./SlogansListItem"
import PageSeparator from "./PageSeparator"

type SlogansListPageProps = {
    response: SloganResponse,
    currentUser: User
}

function SlogansListPage(props: SlogansListPageProps) {
    const pageNum = props.response.meta.page
    const currentUser = props.currentUser
    return (
        <React.Fragment>
            <PageSeparator pageNum={pageNum} />
            {props.response.slogans.map((slogan) => {
                return <SlogansListItem 
                    slogan={slogan}
                    currentUser={currentUser}
                    key={slogan.rowid} 
                />
            })}
        </React.Fragment>
    )
}

export default SlogansListPage
   