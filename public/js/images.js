let links = document.querySelectorAll('[data-delete]')

for (link of links) {
    link.onclick = (e) => {
        e.preventDefault()

        // confirmation

        if (confirm('Voulez vous supprimer cette images ?')) {
            target = e.target.parentNode

            fetch(target.getAttribute('href'), {
                method: "DELETE",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ "_token": target.dataset.token })
            }).then(
                response => response.json()
            ).then(data => {
                if (data.success) {
                    target.parentNode.parentNode.parentNode.remove()
                } else {
                    alert('error')
                }
            }).catch(e => alert(e))
        }
    }
}