import React, { PropsWithChildren } from "react"
import {
  render, RenderOptions, screen
} from "@testing-library/react"
import Header from "../components/Header"
import UserContext, { defaultUser } from "../contexts/UserContext"
import type { CurrentUserContext } from "../types"

type ProviderProps = Omit<RenderOptions, "wrapper"> & { value: CurrentUserContext }

const UserContextProviderWrapper = (
  providerProps: ProviderProps
): React.FC => {
  return ({ children }: PropsWithChildren<Record<never, never>>) => (
    <UserContext.Provider {...providerProps}>
      {children}
    </UserContext.Provider>
  )
}

test("renders default username when user is not logged in, redux", () => {
  const providerProps = {
    value: {
      loading: false,
      currentUser: defaultUser
    }
  }
  const wrapper = UserContextProviderWrapper(providerProps)
  render(<Header />, { wrapper })

  const headerText = screen.getByText(/Sloganator/i)
  expect(headerText).toBeInTheDocument()

  const welcomeText = screen.getByText(/Hello/i)
  expect(welcomeText).toBeInTheDocument()
  expect(welcomeText).toHaveTextContent("Hello, Treefort Lover")
})

test("renders the username, redux", () => {
  const providerProps = {
    value: {
      loading: false,
      currentUser: {
        userId: 100,
        userName: "Goober Face"
      }
    }
  }
  const wrapper = UserContextProviderWrapper(providerProps)
  render(<Header />, { wrapper })

  const headerText = screen.getByText(/Sloganator/i)
  expect(headerText).toBeInTheDocument()

  const welcomeText = screen.getByText(/Hello/i)
  expect(welcomeText).toBeInTheDocument()
  expect(welcomeText).toHaveTextContent("Hello, Goober Face")
})
