import axios from "axios"
import { SloganResponse, SloganErrorResponse } from "../types"

let apiHost = window.location.origin
if (process.env.NODE_ENV === "development") {
    apiHost = `http://${process.env.REACT_APP_API_HOST}`
}

const BASE_PATH = "/mies/sloganator"
const SLOGANS_PATH = `${BASE_PATH}/v1/slogans`

const sloganatorClient = axios.create({
    baseURL: apiHost
})

type GetSloganParams = {
    page?: number,
    author?: number
}

const handleException = ((error: any): Promise<SloganErrorResponse> => {
    console.log(error.config);
    if (error.response) {
        // The request was made and the server 
        // responded with a status code that 
        // falls out of the range of 2xx
        console.log(error.response.data);
        console.log(error.response.status);
        console.log(error.response.headers);
        return Promise.reject(error.response.data)
      } else if (error.request) {
        // The request was made but no response 
        // was received `error.request` is an 
        // instance of XMLHttpRequest in the browser 
        // and an instance of http.ClientRequest in node.js
        console.log(error.request);
        return Promise.reject({
            code: 503,
            message: "Service Unavailable"
        })
      } else {
        // Something happened in setting up the 
        // request that triggered an Error
        console.log('Error', error.message);
        return Promise.reject({
            code: 0,
            message: "Client configuration error"
        })
      }
})

const getSlogans = (params: GetSloganParams): 
    Promise<SloganErrorResponse | SloganResponse> => {
    const queryParams = new URLSearchParams()
    
    if (params.author) {
        queryParams.set('author', params.author.toString())
    }

    if (params.page) {
        queryParams.set('page', params.page.toString())
    }

    return sloganatorClient.get(`${SLOGANS_PATH}?` + queryParams)
        .then((response) => response.data)
        .catch(handleException)
}

export default sloganatorClient
export { getSlogans }
