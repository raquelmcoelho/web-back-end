function ById(id) {
  return document.getElementById(id);
}

function recherche_auteurs() {
  let debnom = ById("debnom").value;
  if(debnom == "") {
    affiche_auteurs([]);
    return null;
}
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      affiche_auteurs(JSON.parse(xhr.responseText));
    }
  };
  xhr.open(
    "GET",
    "recherche_auteurs.php?debnom=" + encodeURIComponent(debnom),
    true
  );
  xhr.send(null);
}

function affiche_auteurs(auteurs) {
  let divGauche = ById("div-gauche");
  divGauche.innerHTML = "<ol>";
  auteurs.forEach((auteur) => {
    divGauche.innerHTML += `<li><a href="#" onclick="recherche_ouvrages_auteur(${auteur.code})">${auteur.nom} ${auteur.prenom}</a></li>`;
  });
  divGauche.innerHTML += "</ol>";
}

function recherche_ouvrages_titre() {
  let debtitre = ById("debtitre").value;
  if(!debtitre) {
    affiche_ouvrages([]);
    return null;
  }
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      affiche_ouvrages(JSON.parse(xhr.responseText));
    }
  };
  xhr.open(
    "GET",
    "recherche_ouvrages_titre.php?debtitre=" + encodeURIComponent(debtitre),
    true
  );
  xhr.send();
}

function affiche_ouvrages(ouvrages) {
  let divDroite = ById("div-droite");
  divDroite.innerHTML = "<ol>";
  ouvrages.forEach((ouvrage) => {
    console.log(ouvrage);
    divDroite.innerHTML += `<li>${ouvrage.nom}<ul id="ul-${ouvrage.code}"></ul></li>`;
    affiche_exemplaires(ouvrage.code, ouvrage.exemplaires);
  });
  divDroite.innerHTML += "</ol>";
}

function affiche_exemplaires(ouvrageCode, exemplaires) {
  let ul = ById(`ul-${ouvrageCode}`);
  exemplaires = JSON.parse(exemplaires);
  if (exemplaires.length === 0) {
    ul.innerHTML += `<li>Aucun exemplaire disponible</li>`;
  }
  if (exemplaires.length > 0) {
    ul.innerHTML += `<li>Exemplaires disponibles:</li>`;
  }
  exemplaires.forEach((exemplaire) => {
    console.log(exemplaire);
    ul.innerHTML = `<li>Code: ${exemplaire.code}`;
    if (exemplaire.prix !== null) {
      ul.innerHTML += `Prix: ${exemplaire.prix} euros`;
      ul.innerHTML += `<button onclick='ajouter_panier(${exemplaire.code})'> 🛒 Ajouter </button>`;
    } else {
      // TODO: decorar
      ul.innerHTML += `Produit Indisponible`;
    }
    ul.innerHTML += `</li>`;
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

function ajouter_panier(code_exemplaire) {
  fetch("panier.php?action=add", {
    method: "POST",
    body: new URLSearchParams({
      code_exemplaire
    }),
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
    .then((response) => response.json())
    .then((data) => alert(data.message))
    .catch((error) => console.error("Erreur:", error));
}

function remove_livre(code_exemplaire) {
  fetch("panier.php?action=remove", {
    method: "POST",
    body: new URLSearchParams({ code_exemplaire }),
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
    .then((response) => response.json())
    .then((data) => {
      alert(data.message);
      recharger_panier();
    })
    .catch((error) => console.error("Erreur:", error));
}

function recharger_panier() {
  // TODO: change to Ajax
  fetch("panier.php?action=afficher")
  .then(response => response.json())
  .then(panier => {
      let panierDiv = document.getElementById("panier-div");
      if (panier.length === 0) {
          panierDiv.innerHTML = "<p>Votre panier est vide.</p>";
      } else {
          let html = " <h1>Votre Panier</h1><ul>";
          let prix = 0.0;
          
          panier.forEach(item => {
              prix += parseFloat(item.prix);
              html += `<li>${item.nom} - ${item.editeur} (Quantité: ${item.quantite}) (Prix: <?= htmlspecialchars(${item.prix}) ?> €)
                       <button onclick="remove_livre(${item.code_exemplaire})">Supprimer</button></li>`;
          });

          html += `<li><strong>Total: ${prix.toFixed(2)} €</strong></li>`;
          html += "</ul>";
          panierDiv.innerHTML = html;
      }

      panierDiv.innerHTML += "<button type='button' onclick='montrer_recherche()'>Fermer</button>";
      panierDiv.innerHTML += `<button type='button' onclick='commander()'>Commander</button>`;
  })
  .catch(error => console.error("Erreur:", error));
}

// source: https://stackoverflow.com/questions/10730362/get-cookie-by-name
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

function commander() {
  let code_client = getCookie("code_client");
  if (code_client) {
    fetch("panier.php?action=commander", {
      method: "POST",
      body: new URLSearchParams({ code_client }),
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message);
        recharger_panier();
      })
      .catch((error) => console.error("Erreur:", error));
  } else {
    alert("Vous devez vous inscrire pour commander.");
    montrer_formulaire();
  }
}

function montrer_panier() {
  recharger_panier();
  document.getElementById("form-div").style.display = "none";
  document.getElementById("search-div").style.display = "none";
  document.getElementById("panier-div").style.display = "block";
}

function montrer_recherche() {
  document.getElementById("form-div").style.display = "none";
  document.getElementById("search-div").style.display = "block";
  document.getElementById("panier-div").style.display = "none";
}

function montrer_formulaire() {
  document.getElementById("form-div").style.display = "block";
  document.getElementById("search-div").style.display = "none";
  document.getElementById("panier-div").style.display = "none";
}

function enregistrement() {
    $.ajax({
        type: "POST",
        url: "inscription.php",
        data: {
            nom: $("#nom").val(),
            prenom: $("#prenom").val(),
            adresse: $("#adresse").val(),
            code_postal: $("#code_postal").val(),
            ville: $("#ville").val(),
            pays: $("#pays").val()
        },
        dataType: "json",
        success: function(data) {
            if (data.success) {
                document.cookie = `code_client=${data.code_client}; expires=Fri, 31 Dec 2050 23:59:59 GMT; path=/`;
                alert("Inscription réussie !");
                window.location.href = "index.php";
                montrer_recherche();
            } else {
                $("#messageErreur").html(`<p style="color:red;">${data.message}</p>`);
            }
        }
    });
}

function deconnecter() {
  // TODO: sauvegarder panier
  // TODO: effacer cookie?
}
