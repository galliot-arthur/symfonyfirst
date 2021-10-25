let switches = document.querySelectorAll(".switches")

for (let switchable of switches) {
    switchable.onclick = (e) => {
        let xmlhttp = new XMLHttpRequest;
        xmlhttp.open("get", `/admin/annonces/activer/${e.target.dataset.id}`);
        xmlhttp.send();
    }
}


/* switchable.addEvenListener("click", () => {
    let xmlhttp = new XMLHttpRequest;
    xmlhttp.open("get", '/admin/annonces/activer/${this.dataset.id}');
    xmlhttp.send();
}) */