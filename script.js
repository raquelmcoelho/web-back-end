function recherche_auteurs() {
    let debnom = document.getElementById("input-auteur").value;
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "recherche_auteurs.php?debnom=" + encodeURIComponent(debnom), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            affiche_auteurs(JSON.parse(xhr.responseText));
        }
    };
    xhr.send();
}

function affiche_auteurs(auteurs) {
    let divGauche = document.getElementById("div-gauche");
    divGauche.innerHTML = "<ol>";
    auteurs.forEach(auteur => {
        divGauche.innerHTML += `<li><a href="#" onclick="recherche_ouvrages_auteur(${auteur.code})">${auteur.nom} ${auteur.prenom}</a></li>`;
    });
    divGauche.innerHTML += "</ol>";
}

function recherche_ouvrages_titre() {
    let debtitre = document.getElementById("input-livre").value;
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "recherche_ouvrages_titre.php?debtitre=" + encodeURIComponent(debtitre), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            affiche_ouvrages(JSON.parse(xhr.responseText));
        }
    };
    xhr.send();
}

function affiche_ouvrages(ouvrages) {
    let divDroite = document.getElementById("div-droite");
    divDroite.innerHTML = "<ol>";
    ouvrages.forEach(ouvrage => {
        divDroite.innerHTML += `<li>${ouvrage.nom}<ul id="ul-${ouvrage.code}"></ul></li>`;
        affiche_exemplaires(ouvrage.code, ouvrage.exemplaires);
    });
    divDroite.innerHTML += "</ol>";
}

function affiche_exemplaires(ouvrageCode, exemplaires) {
    let ul = document.getElementById(`ul-${ouvrageCode}`);
    exemplaires.forEach(exemplaire => {
        ul.innerHTML += `<li>${exemplaire.nom}, ${exemplaire.prix} euros</li>`;
    });
}

function recherche_ouvrages_auteur(code) {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "recherche_ouvrages_auteur.php?code=" + code, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            affiche_ouvrages(JSON.parse(xhr.responseText));
        }
    };
    xhr.send();
}

function affiche_ouvrages(ouvrages) {
    let divDroite = document.getElementById("div-droite");
    divDroite.innerHTML = "<ol>";
    ouvrages.forEach(ouvrage => {
        divDroite.innerHTML += `
            <li>${ouvrage.nom} 
                <button onclick="addToCart(${ouvrage.code})">🛒 Ajouter</button>
                <ul id="ul-${ouvrage.code}"></ul>
            </li>`;
        affiche_exemplaires(ouvrage.code, ouvrage.exemplaires);
    });
    divDroite.innerHTML += "</ol>";
}

function addToCart(code_ouvrage) {
    fetch('panier.php?action=add', {
        method: 'POST',
        body: new URLSearchParams({ code_ouvrage }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(response => response.json())
      .then(data => alert(data.message))
      .catch(error => console.error('Erreur:', error));
}
