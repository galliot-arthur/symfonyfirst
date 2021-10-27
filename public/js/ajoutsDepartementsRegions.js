let collection, boutonAjout, span;
window.onload = () => {
    collection = document.querySelector('#departements')
    span = collection.querySelector('span')
    boutonAjout = document.createElement('button')
    boutonAjout.className = "ajout-departement btn btn-warning shadow-sm text-dark mb-2"
    boutonAjout.innerText = "Ajouter un département"

    let newButton = span.append(boutonAjout)

    collection.dataset.index = collection
        .querySelectorAll('input')
        .length

    boutonAjout.addEventListener('click', (e) => {
        e.preventDefault()
            //console.log('yo')
        addButton(collection, newButton)
    })
}

function addButton(collection, nouveauBouton) {
    let prototype = collection.dataset.prototype
    let index = collection.dataset.index
    prototype = prototype.replace(/__name__/g, index)
    let content = document.createElement('html')
    content.innerHTML = prototype
    let newForm = content.querySelector('div')

    let deleteButton = document.createElement('button')
    deleteButton.type = "button"
    deleteButton.className = "btn btn-warning shadow-sm text-dark mb-2"
    deleteButton.id = "ajout-departement " + index
    deleteButton.innerText = "Supprimer ce département"
    newForm.append(deleteButton)

    collection.dataset.index++;

    let addButton = collection.querySelector('.ajout-departement')

    span.insertBefore(newForm, addButton)

    // delete

    deleteButton.addEventListener('click', function() {
        this.previousElementSibling.parentElement.remove()
    })
}