function PageSeparator(props: { pageNum: number }) {
    return(
        <li className="divider" key={`sep-${props.pageNum}`}>
            &lt;-- page {props.pageNum} --&gt;
        </li>
    )
}

export default PageSeparator
