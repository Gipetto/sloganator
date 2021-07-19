const header = document.getElementsByTagName("header")[0]
const footer = document.getElementsByTagName("footer")[0]
const filter = document.querySelector("#filter")
const chartDiv = document.querySelector("#chart");

const resizeChartSpace = () => {
    const headerHeight = header.offsetHeight
    const footerHeight = footer.offsetHeight
    const filterHeight = filter.offsetHeight
    chartDiv.style.height = window.innerHeight - (headerHeight + footerHeight + filterHeight + 80)
}

const fluffyBunnies = () => {
    resizeChartSpace()
    getAuthors()
    
    anychart.onDocumentReady(() => {
        const chart = anychart.tagCloud(data)

        chart.angles([0])
            .textSpacing(3)
            .container("chart")
            .draw()
    })
}

'loading' === document.readyState ?
    document.addEventListener('DOMContentLoaded', fluffyBunnies, false) :
    fluffyBunnies();

window.addEventListener("resize", resizeChartSpace);