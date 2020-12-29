const sloganatorHtml = 
'<div id="sloganator">' +
	'<span class="slogan" title=""></span>' +
	'<span class="sloganator-form" style="display: none;">' +
		'<form>' +
			'<input name="slogan" type="text">' +
			'<button class="slogan-submit" disabled><span>Add Slogan</span></button>' +
			'<button class="slogan-cancel">&times;<span>Cancel</span></button>' +
			'<a href="/mies/sloganator">Sloganator <i class="fa fa-external-link-square"></a></a>' +
		'</form>' +
	'</span>' +
'</div>';

function loadTemplate() {
	let template = document.createElement("template");
	let html = sloganatorHtml.trim();
	
	template.innerHTML = html;
	
	return template.content.firstChild;
}

function attachEventListeners(sloganator) {
	let form = sloganator.querySelector(".sloganator-form");
	let submitButton = form.querySelector(".slogan-submit");

	sloganator.querySelector(".slogan").addEventListener("click", (event) => {
		event.preventDefault();
		event.stopPropagation();
		form.style.display = "";
	    sloganator.classList.add("open");
    }, false);

	sloganator.querySelector("form input[name='slogan']").addEventListener("keyup", (event) => {
		let _value = event.target.value;
		submitButton.toggleAttribute("disabled", !_value.length);
	}, false);

	sloganator.querySelector("form .slogan-cancel").addEventListener("click", (event) => {
		event.preventDefault();
		event.stopPropagation();
		form.style.display = "none";
        sloganator.classList.remove("open");
	}, false);

	sloganator.querySelector("form").addEventListener("submit", (event) => {
		event.preventDefault();
		event.stopPropagation();
		
		let newSlogan = form.querySelector("input[name='slogan']").value.trim();

		if (!newSlogan.length) {
			return false;
		}		
	
		addSlogan(newSlogan);
	}, false);
}

function addSlogan(newSlogan) {
	let url = new URL(window.location.origin + "/mies/sloganator/v1/slogans");	
	let params = {
		headers: {
			"Accept": "application/json",
			"Content-Type": "application/json"
		},
		method: 'POST',
		body: JSON.stringify({"slogan": newSlogan})
	}
	fetch(url, params)
		.then(res => res.json())
		.then(res => writeSlogan(res));
}

function clearStage() {
	let sloganator = document.querySelector('#sloganator');
	if (sloganator) {
		sloganator.remove();
	}
}

function writeSlogan(slogan) {
	clearStage();
	let sloganator = loadTemplate();
    let sl = sloganator.querySelector(".slogan");
	sl.innerText = slogan.slogan;
	sl.title = "Authored By: " + slogan.username;

    attachEventListeners(sloganator);

	let wrapper = document.querySelector('#logo .wrapper');
	wrapper.appendChild(sloganator);
}

function getSlogan(pageNum = 1) {
    let url = new URL(window.location.origin + "/mies/sloganator/v1/slogans");
    url.search = new URLSearchParams({
        page: 1,
        pageSize: 1
    });

    let params = {
        headers: {
            "Accept": "application/json"
        }
    };

    fetch(url, params)
        .then(res => res.json())
        .then(res => writeSlogan(res.slogans[0]));
}

"loading" === document.readyState ?
    document.addEventListener("DOMContentLoaded", (event) => { getSlogan(); }, false) :
    getSlogan();
