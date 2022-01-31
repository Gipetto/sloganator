import React from "react"
import { UserProvider } from "./contexts/UserContext"
import Header from "./components/Header"
import Footer from "./components/Footer"
import SlogansList from "./components/SlogansList"


class App extends React.Component {
  render() {
    return (
      <UserProvider>
        <div id="content">
          <Header/>
          <SlogansList/>
          <Footer/>
        </div>
      </UserProvider>
    )
  }
}

export default App
