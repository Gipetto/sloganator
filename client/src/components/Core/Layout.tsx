/* global HTMLElementTagNameMap */
import React, { PropsWithChildren } from "react"
import "../../styles/Layout.scss"

interface Flex {
  justifyContent?: "flex-start" | "flex-end" | "center" | "space-between" | "space-around" | "space-evenly" | undefined
  flexDirection?: "row" | "row-reverse" | "column" | "column-reverse" | undefined
  alignItems?: "stretch" | "flex-start" | "flex-end" | "center" | "baseline" | "auto" | undefined
  flex?: string | undefined
  gap?: string | undefined
}

type LayoutProps = PropsWithChildren<{
  className?: string | undefined
  as?: keyof HTMLElementTagNameMap

}> & React.HTMLProps<HTMLElement> & Flex

const LayoutElement = (props: LayoutProps) => {
  const {
    as: tag,
    justifyContent,
    flexDirection,
    alignItems,
    flex,
    gap,
    children,
    ...rest
  } = props

  const styles: Flex = {
    justifyContent,
    flexDirection,
    alignItems,
    flex,
    gap
  }

  const style = Object.entries(styles)
    .reduce(
      (acc, [k, v]) => (v !== undefined ? { ...acc, [k]: v } : acc),
      {}
    )

  const type = tag || "div"
  return React.createElement(type, { style, ...rest }, children)
}

/**
 * Parent Grid wrapper.
 * Reuse everywhere you want to start a new grid.
 *
 * @param props GridProps
 * @returns GridElement
 */
const Layout = (props: LayoutProps) => {
  const { children, className, ...rest } = props
  const cls = `grid ${className || ""}`
  return (
    <LayoutElement className={cls} {...rest}>
      {children}
    </LayoutElement>
  )
}

const LayoutRow = (props: LayoutProps) => {
  const { children, className, ...rest } = props
  const cls = `grid grid-row ${className || ""}`
  return (
    <LayoutElement className={cls} {...rest}>
      {children}
    </LayoutElement>
  )
}

const LayoutCol = (props: LayoutProps) => {
  const { children, className, ...rest } = props
  const cls = `grid grid-col ${className || ""}`
  return (
    <LayoutElement className={cls} {...rest}>
      {children}
    </LayoutElement>
  )
}

const LayoutCell = (props: LayoutProps) => {
  const { children, className, ...rest } = props
  const cls = `grid-cell ${className || ""}`
  return (
    <LayoutElement className={cls} {...rest}>
      {children}
    </LayoutElement>
  )
}

export default Layout
export {
  LayoutCell,
  LayoutCol,
  LayoutRow
}
