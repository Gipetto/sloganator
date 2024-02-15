import { useSlogansContext } from "./contexts/SlogansContext"
import Layout from "../../app/src/components/Core/Layout"
import CurrentSlogan from "./components/CurrentSlogan"
import EditorContainer from "./components/EditorContainer"
import Loading from "./components/Loading"

import "./styles/Widget.scss"
import { Sticky } from "./components/EightDotOne"

const Widget = () => {
  const {
    state: slogansContext,
    actions: { setEditing },
  } = useSlogansContext()

  return (
    <Layout className="widget eight-dot-one">
      {slogansContext.loading && <Loading />}
      {!slogansContext.loading && (
        <>
          <EditorContainer />
          <Sticky>
            <CurrentSlogan
              slogan={slogansContext.slogans[0]}
              setEditing={setEditing}
            />
          </Sticky>
        </>
      )}
    </Layout>
  )
}

export default Widget
