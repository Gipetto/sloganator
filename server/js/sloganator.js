const init = () => {
  const wrapper = document.querySelector("#logo .wrapper");
  const target = document.createElement("div");
  target.id = "sloganator";
  wrapper.appendChild(target);

  fetch("/mies/sloganator/manifest.json")
    .then((response) => {
      return response.json();
    })
    .then((json) => {
      const headElement = document.getElementsByTagName("head")[0];
      const pathPrefix = "/mies/sloganator";
      const manifest = json["widget/index.html"];

      manifest["css"].forEach((stylePath) => {
        const _style = document.createElement("link");
        _style.rel = "stylesheet";
        _style.href = `${pathPrefix}/${stylePath}`;
        headElement.appendChild(_style);
      });

      const mainJs = document.createElement("script");
      mainJs.type = "module";
      mainJs.src = `${pathPrefix}/${manifest.file}`;
      headElement.appendChild(mainJs);
    });
};

"loading" === document.readyState
  ? document.addEventListener(
      "DOMContentLoaded",
      () => {
        init();
      },
      false
    )
  : init();
