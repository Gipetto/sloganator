"use strict"; 

let loader = document.querySelector(".loader");
let figure = document.querySelector("#templates figure");
let currentUserId = parseInt(document.querySelector(".current-user").dataset.id);
let slogansList = document.querySelector("#slogans ul");

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

    updateLoader(page + 1);
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

function getSlogans(pageNum = 1) {
    let loading = document.createElement("li");
    loading.classList.add("loading");
    loading.innerText = "<-- loading " + pageNum.toString() + " -->";
    slogansList.appendChild(loading);

    let url = new URL(window.location.origin + "/mies/sloganator/v1/slogans");
    url.search = new URLSearchParams({
        page: pageNum
    });

    let params = {
        headers: {
            "Accept": "application/json"
        }
    };

    fetch(url, params)
        .then(res => res.json())
        .then(res => {
            loading.remove();
            writeSlogans(res);
        });
}

function updateLoader(page) {
    loader.dataset.page = page;
    loader.querySelector(".page").innerText = page;
}

loader.addEventListener("click", (event) => {
    let _this = event.target;
    getSlogans(_this.dataset.page);
}, false);

"loading" === document.readyState ?
    document.addEventListener("DOMContentLoaded", (event) => { getSlogans(); }, false) :
    getSlogans();