const init = () => {
    const wrapper = document.querySelector("#logo .wrapper")
    const target = document.createElement("div")
    target.id = "sloganator"
    wrapper.appendChild(target)

    fetch("sloganator/client/build/manifest.json")
        .then((response) => {
            return response.json()
        })
        .then((json) => {
            const headElement = document.getElementsByTagName("head")[0]
            const pathPrefix = "sloganator/client/build"
            const manifest = json["widget/index.html"]

            manifest["css"].forEach(stylePath => {
                const _style = document.createElement("link")
                _style.rel = "stylesheet"
                _style.href = `${pathPrefix}/${stylePath}`
                headElement.appendChild(_style)
            })

            const mainJs = document.createElement("script")
            mainJs.type = "module"
            mainJs.src = `${pathPrefix}/${manifest.file}`
            headElement.appendChild(mainJs)
        })
}

init()
