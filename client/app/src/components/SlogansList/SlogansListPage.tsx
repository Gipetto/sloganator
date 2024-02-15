import type { Slogan, SlogansResponse, User } from "../../types"
import SlogansListItem from "./SlogansListItem"
import PageSeparator from "./PageSeparator"

interface SlogansListPageProps {
  response: SlogansResponse
  currentUser: User
}

const SlogansListPage = (props: SlogansListPageProps) => {
  const pageNum = props.response.meta.page
  const { currentUser } = props
  return (
    <>
      <PageSeparator pageNum={pageNum} />
      {props.response.slogans.map((slogan: Slogan) => (
        <SlogansListItem
          slogan={slogan}
          currentUser={currentUser}
          key={slogan.rowid}
        />
      ))}
    </>
  )
}

export default SlogansListPage
