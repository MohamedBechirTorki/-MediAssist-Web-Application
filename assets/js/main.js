

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