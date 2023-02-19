import { useSlogansContext } from "./contexts/SlogansContext"
import Layout from "../../app/src/components/Core/Layout"
import CurrentSlogan from "./components/CurrentSlogan"
import EditorContainer from "./components/EditorContainer"
import Loading from "./components/Loading"

import "./styles/Widget.scss"

const Widget = () => {
  const {
    state: slogansContext,
    actions: { setEditing },
  } = useSlogansContext()

  return (
    <Layout className="widget">
      {slogansContext.loading && <Loading />}
      {!slogansContext.loading && slogansContext.editing && <EditorContainer />}
      {!slogansContext.loading && (
        <CurrentSlogan
          slogan={slogansContext.slogans[0]}
          setEditing={setEditing}
        />
      )}
    </Layout>
  )
}

export default Widget
