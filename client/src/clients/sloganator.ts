import axios from "axios"

let apiHost = window.location.origin
if (process.env.NODE_ENV === "development") {
    apiHost = `http://${process.env.REACT_APP_API_HOST}`
}

const sloganatorClient = axios.create({
    baseURL: apiHost
})

export default sloganatorClient
