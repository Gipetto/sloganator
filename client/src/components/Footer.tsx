import { LayoutCell } from "./Core/Layout"

function Footer() {
  const year = new Date().getFullYear()

  return (
    <LayoutCell as="footer">
      <p>
        &copy;
        {" "}
        {year}
        , not by you. |
        {" "}
        <a href="https://github.com/Gipetto/sloganator">
          Marvel in this code’s mediocrity
        </a>
      </p>
    </LayoutCell>
  )
}

export default Footer
