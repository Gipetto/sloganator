interface PageSeparatorProps {
  pageNum: number
}

const PageSeparator = (props: PageSeparatorProps) => {
  return (
    <li className="divider" key={`sep-${props.pageNum}`}>
      &lt;-- page {props.pageNum} --&gt;
    </li>
  )
}

export default PageSeparator
