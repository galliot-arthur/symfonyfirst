window.onload = () => {
    let region = document.querySelector('#annonces_types_regions')
    region.addEventListener('input', function() {
        let form = this.closest('form')
        let data = this.name + "=" + this.value

        fetch(form.action, {
                method: form.getAttribute("method"),
                body: data,
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset:UTF-8"
                }
            })
            .then(response => response.text())
            .then(html => {
                let content = document.createElement('html')
                content.innerHTML = html
                let newSelect = content.querySelector('#annonces_types_departements')
                document
                    .querySelector('#annonces_types_departements')
                    .replaceWith(newSelect)
            })
            .catch(e => console.log(e))
    })
}