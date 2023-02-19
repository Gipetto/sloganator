import React from "react"
import ReactDOM from "react-dom"
import Widget from "./Widget"
import reportWebVitals from "./reportWebVitals"
import { SlogansContextProvider } from "./contexts/SlogansContext"
import "./styles/Sloganator.scss"

ReactDOM.render(
  <React.StrictMode>
    <SlogansContextProvider>
      <Widget />
    </SlogansContextProvider>
  </React.StrictMode>,
  document.getElementById("sloganator")
)

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals(console.log)
