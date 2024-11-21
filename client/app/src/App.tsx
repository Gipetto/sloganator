import { UserContextProvider } from "./contexts/UserContext"
import { AuthorContextProvider } from "./contexts/AuthorContext"
import Header from "./components/Header"
import Footer from "./components/Footer"
import SlogansList from "./components/SlogansList/SlogansList"
import Layout, { LayoutCol } from "./components/Core/Layout"
import "./styles/App.scss"

const App = () => (
  <Layout className="grid app">
    <LayoutCol className="app-container">
      <UserContextProvider>
        <Header />
        <AuthorContextProvider>
          <SlogansList />
        </AuthorContextProvider>
        <Footer />
      </UserContextProvider>
    </LayoutCol>
  </Layout>
)

export default App
