function Footer() {
    let year = new Date().getFullYear()

    return (
        <footer>
            <p>&copy; {year}, not by you. | <a href="https://github.com/Gipetto/sloganator">Marvel in this code’s mediocrity</a></p>
        </footer>
    )
}

export default Footer
