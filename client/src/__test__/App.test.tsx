import { render, screen } from "@testing-library/react"
import App from "../App"

test("renders loading when user context not ready", () => {
  render(<App />)
  const headerText = screen.getByText(/Loading.../i)
  expect(headerText).toBeInTheDocument()
})
