// jest-dom adds custom jest matchers for asserting on DOM nodes.
// allows you to do things like:
// expect(element).toHaveTextContent(/react/i)
// learn more: https://github.com/testing-library/jest-dom
import "@testing-library/jest-dom"
import React from "react"
import {
  render, RenderOptions, RenderResult
} from "@testing-library/react"

const customRender = (
  ui: React.ReactElement,
  wrapper: React.FC,
  renderOptions: Omit<RenderOptions, "wrapper"> = {}
): RenderResult => render(ui, { ...renderOptions, wrapper })

export {
  customRender
}
