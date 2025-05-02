

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


function ajouterChampTemps() {
    const container = document.getElementById('temps-container');
    const lastInput = container.querySelector('.temps-line:last-child input');

    if (!lastInput.value) {
        alert("Veuillez remplir l'heure précédente avant d'en ajouter une autre.");
        return;
    }

    const div = document.createElement('div');
    div.classList.add('temps-line');

    const input = document.createElement('input');
    input.type = 'time';
    input.name = 'temps[]';
    input.required = true;

    const button = document.createElement('button');
    button.type = 'button';
    button.innerText = '-';
    button.onclick = function () {
        supprimerChampTemps(this);
    };

    div.appendChild(input);
    div.appendChild(button);
    container.appendChild(div);
}

function supprimerChampTemps(button) {
    const container = document.getElementById('temps-container');
    const total = container.querySelectorAll('.temps-line').length;
    const input = button.previousElementSibling;

    // Vérifie si l'heure est vide
    if (!input.value) {
        alert("Ce champ est vide. Vous ne pouvez pas supprimer un champ vide.");
        return;
    }

    // Autoriser la suppression seulement si plus d’un champ existe
    if (total > 1) {
        button.parentElement.remove();
    } else {
        alert("Au moins une heure est requise.");
    }
}


function ouvrirModal(id) {
    // Simule l’appel AJAX ou récupération manuelle (à remplacer par un vrai appel si besoin)
    fetch(`get_medicament.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modif_id').value = data.id;
            document.getElementById('modif_nom').value = data.nom;
            document.getElementById('modif_debut').value = data.debut;
            document.getElementById('modif_fin').value = data.fin;

            const container = document.getElementById('modif_temps_container');
            container.innerHTML = ''; // Clear previous inputs

            data.temps.forEach(time => {
                const div = document.createElement('div');
                div.className = 'temps-line';
                div.innerHTML = `
                    <input type="time" name="temps[]" value="${time}">
                    <button type="button" onclick="supprimerChampTemps(this)">−</button>
                `;
                container.appendChild(div);
            });

            // Ajoute un champ vide par défaut si aucun temps existant
            if (data.temps.length === 0) ajouterChampTempsModif();

            document.getElementById('modalModifier').style.display = 'flex';
        });
}

function fermerModal() {
    document.getElementById('modalModifier').style.display = 'none';
}

function ajouterChampTempsModif() {
    const container = document.getElementById('modif_temps_container');

    const div = document.createElement('div');
    div.className = 'temps-line';
    div.innerHTML = `
        <input type="time" name="temps[]">
        <button type="button" onclick="supprimerChampTemps(this)">−</button>
    `;
    container.appendChild(div);
}

function supprimerChampTemps(btn) {
    btn.parentNode.remove();
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




  

document.addEventListener('DOMContentLoaded', function() {
    // Event listener pour les boutons de suppression
    const deleteButtons = document.querySelectorAll('.delete-button');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const ordonnanceId = button.getAttribute('data-id');
            
            // Requête AJAX pour supprimer l'ordonnance
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete_ordonnance.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Si la suppression est réussie, on enlève l'élément de l'interface
                    if (xhr.responseText == 'success') {
                        button.parentElement.remove();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                }
            };
            xhr.send('id=' + ordonnanceId);
        });
    });
});
