let likes = document.querySelectorAll('[data-like]')

for (like of likes) {
    like.onclick = (e) => {
        e.preventDefault()

        target = e.target.parentNode
            // confirmation
            //console.log(target.dataset.like)
            //target.getAttribute('href')
        let xmlhttp = new XMLHttpRequest;
        xmlhttp.open("get", `/users/annonces/like/${target.dataset.like}`, false);
        xmlhttp.send();

        window.location.reload()
    }
}