

let darkStylsheet = document.getElementById('darkMode');
darkStylsheet.disabled = true;
let lightDarkBtn = document.getElementById('changeColor');
let lightDarkPic = document.querySelector('#changeColor img');
lightDarkBtn.onclick = () => {
    console.log("clicked");
    darkStylsheet.disabled = !darkStylsheet.disabled;
    if (!darkStylsheet.disabled) {
        lightDarkPic.src = "./assets/images/sun.png"
    }
    else {
        lightDarkPic.src = "./assets/images/moon.jpg"
    }
}


function ouvrirModal(id, nom, posologie, frequence, debut, fin) {
    document.getElementById('modif_id').value = id;
    document.getElementById('modif_nom').value = nom;
    document.getElementById('modif_posologie').value = posologie;
    document.getElementById('modif_frequence').value = frequence;
    document.getElementById('modif_debut').value = debut;
    document.getElementById('modif_fin').value = fin;

    document.getElementById('modalModifier').style.display = 'flex';
}

function fermerModal() {
    document.getElementById('modalModifier').style.display = 'none';
}

let artButtons = document.querySelectorAll(".dash-nav div");
let articles = document.querySelectorAll(".dash-slider article");
console.log(artButtons);
console.log(articles);

for (let i = 1; i < articles.length; i++) {
    articles[i].style.display = "none";
}


for (let i = 0; i < artButtons.length; i++) {
    artButtons[i].onclick = () => {
        for (let j = 0; j < artButtons.length; j++) {
            artButtons[j].className = "";
        }
        artButtons[i].className = "active";

        console.log("clicked");
        for (let j = 0; j < articles.length; j++) {
            articles[j].style.display = "none";
        }
        articles[i].style.display = "block";
    }
}
