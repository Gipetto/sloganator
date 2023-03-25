import React, { MouseEvent } from "react"
import "../styles/EightDotOne.scss"

interface WithChildren {
  children: React.ReactNode
}

interface TitleBarProps {
  title: string
  onClose: (e: MouseEvent<HTMLElement>) => void
  onZoom: (e: MouseEvent<HTMLElement>) => void
  onWindowshade: (e: MouseEvent<HTMLElement>) => void
}

const TitleBar = ({ title, onClose, onZoom, onWindowshade }: TitleBarProps) => {
  return (
    <div className="title-bar">
      <div
        className="control-box close-box"
        onClick={(e) => {
          if (onClose) {
            onClose(e)
          }
        }}
      >
        <a className="control-box-inner"></a>
      </div>
      <div className="spacer">&nbsp;</div>
      <h1 className="title">{title}</h1>
      <div className="spacer">&nbsp;</div>
      <div
        className="control-box zoom-box"
        onClick={(e) => {
          if (onZoom) {
            onZoom(e)
          }
        }}
      >
        <div className="control-box-inner">
          <div className="zoom-box-inner"></div>
        </div>
      </div>
      <div
        className="control-box windowshade-box"
        onClick={(e) => {
          if (onWindowshade) {
            onWindowshade(e)
          }
        }}
      >
        <div className="control-box-inner">
          <div className="windowshade-box-inner"></div>
        </div>
      </div>
    </div>
  )
}

interface InnerProps extends WithChildren {}

const Inner = ({ children }: InnerProps) => {
  return <div className="inner">{children}</div>
}

interface PanelProps extends WithChildren {}

const Panel = ({ children }: PanelProps) => {
  return <div className="panel">{children}</div>
}

const ListPanel = ({ children }: PanelProps) => {
  return (
    <div className="panel list">
      <ol>{children}</ol>
    </div>
  )
}

interface ListPanelItemProps extends WithChildren {}

const ListPanelItem = ({ children }: ListPanelItemProps) => {
  return <li className="hover">{children}</li>
}

interface DialogProps extends WithChildren, TitleBarProps {}

const Dialog = ({
  children,
  title,
  onClose,
  onWindowshade,
  onZoom,
}: DialogProps) => {
  return (
    <div className="dialog">
      <TitleBar
        title={title}
        onClose={onClose}
        onWindowshade={onWindowshade}
        onZoom={onZoom}
      />
      <Inner>{children}</Inner>
    </div>
  )
}

interface SystemErrorProps {
  error: {
    code: number | string
    message: string
  }
  onClose: () => void
}
const SystemError = ({ error, onClose }: SystemErrorProps) => {
  return (
    <div className="error">
      <h1 className="title">Sorry, a stupid error occurred.</h1>
      <p>{error.message}</p>
      <div className="controls">
        <button onClick={onClose}>Dammit</button>
        <p>id = {error.code}</p>
      </div>
    </div>
  )
}

interface StickyProps extends WithChildren {}

const Sticky = ({ children }: StickyProps) => {
  return <div className="sticky">{children}</div>
}

export { Dialog, Inner, Panel, ListPanel, ListPanelItem, SystemError, Sticky }
