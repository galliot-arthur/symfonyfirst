const filterForm = document.querySelector('#filterform')

window.onload = () => {
    document
        .querySelectorAll('#filterform input, #filterform select')
        .forEach(input => {
            input.addEventListener("change", () => {
                const Form = new FormData(filterForm)
                const Params = new URLSearchParams()

                // Building the queryString
                Form.forEach((value, key) => {
                    Params.append(key, value)

                })

                // Get active URL
                const Url = new URL(window.location.href)

                Url.pathname == '/' ? path = '' : path = Url.pathname
                fetch(path + "/?" + Params.toString() + "&ajax=1", {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                }).then(response => response.json()).then(data => {
                    const content = document.querySelector('#content')

                    // Replace content
                    content.innerHTML = data.content

                    // Maj URL
                    history.pushState({}, null, path + "/?" + Params.toString())
                }).catch( /* e => alert(e) */ )
            })
        })
}