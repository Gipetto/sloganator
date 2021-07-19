let url = new URL(window.location.href);
let selectedAuthor = url.searchParams.get("author");

function getAuthors() {
    let url = new URL(window.location.origin + "/mies/sloganator/v1/authors");
    let params = {
        headers: {
            "Accept": "application/json"
        }
    }

    fetch(url, params)
        .then(res => res.json())
        .then(res => {
            let sorted = res.sort((a, b) => { 
                return a.usernames[0].localeCompare(b.usernames[0]); 
            });
            writeAuthors(sorted);
        });
}

function writeAuthors(authors) {
    let target = document.querySelector("#filter select");
    authors.forEach(author => {
        let item = document.createElement("option");

        item.value = author.userid;
        item.text = author.usernames.join(", ");

        if (selectedAuthor == author.userid) {
            item.selected = "selected";
        }
        
        target.appendChild(item);
    });

    target.style.visibility = "visible";
}