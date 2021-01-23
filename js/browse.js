"use strict"; 

let loader = document.querySelector(".loader");
let figure = document.querySelector("#templates figure");
let currentUserId = parseInt(document.querySelector(".current-user").dataset.id);
let slogansList = document.querySelector("#slogans ul");
let selectedAuthor = null;

function writeSlogans(slogansResponse) {
    let page = slogansResponse.meta.page;

    let divider = document.createElement("li");
    divider.classList.add("divider");
    divider.innerText = "<-- page " + page.toString() + " -->";

    slogansList.appendChild(divider);

    slogansResponse.slogans.forEach((slogan) => {
        let item = mkRow(slogan);
        let li = document.createElement("li");
        li.appendChild(item);
        slogansList.appendChild(li);
    });

    updateLoader({
        page: slogansResponse.meta.nextPage, 
        filter: slogansResponse.meta.filter
    });
}

function mkRow(slogan) {
    let fig = figure.cloneNode(true);
    fig.querySelector(".slogan").innerText = slogan.slogan
    
    let link = fig.querySelector(".user-name");
    link.href = window.location.origin + "/mies/user-" + slogan.userid.toString() + ".html";
    link.innerText = slogan.username;
    
    let timestamp = new Date(slogan.timestamp * 1000);
    fig.querySelector(".timestamp").innerText = timestamp.toDateString();

    if (currentUserId && slogan.userid === currentUserId) {
        fig.classList.add("current-user-author");
    }

    return fig;
}

function getSlogans(params) {
    let loading = document.createElement("li");
    loading.classList.add("loading");
    loading.innerText = "<-- loading " + params["page"].toString() + " -->";
    slogansList.appendChild(loading);

    let queryParams = {
        page: params["page"]
    };

    if (params["filter"] && params["filter"]["author"]) {
        queryParams["author"] = params["filter"]["author"];
    }

    let url = new URL(window.location.origin + "/mies/sloganator/v1/slogans");
    url.search = new URLSearchParams(queryParams);

    let requestOptions = {
        headers: {
            "Accept": "application/json"
        }
    };

    fetch(url, requestOptions)
        .then(res => res.json())
        .then(res => {
            loading.remove();
            writeSlogans(res);
        });
}

function updateLoader(params) {
    loader.dataset.params = JSON.stringify(params);
    let pageText = "NOTHING! There's nothing more to show…";
    if (params.page) {
        pageText = params.page;
    }
    loader.querySelector(".page").innerText = pageText;
}

loader.addEventListener("click", (event) => {
    event.preventDefault();
    event.stopPropagation();

    let _this = event.target;
    let params = JSON.parse(_this.dataset.params);
    if (params.page) {
        getSlogans(params);
    }
}, false);

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
            writeAuthors(res);
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

function init() {
    let url = new URL(window.location.href);
    selectedAuthor = url.searchParams.get("author");

    getAuthors();
    getSlogans({
        "page": 1,
        "filter": {
            "author": selectedAuthor
        }
    });
}

"loading" === document.readyState ?
    document.addEventListener("DOMContentLoaded", (event) => { init(); }, false) :
    init();