import type { SloganResponse, User } from "../../types"
import SlogansListItem from "./SlogansListItem"
import PageSeparator from "./PageSeparator"

interface SlogansListPageProps {
  response: SloganResponse,
  currentUser: User
}

const SlogansListPage = (props: SlogansListPageProps) => {
  const pageNum = props.response.meta.page
  const { currentUser } = props
  return (
    <>
      <PageSeparator pageNum={pageNum} />
      {props.response.slogans.map((slogan) => (
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
