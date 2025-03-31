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
    affiche_exemplaires(ouvrage.code, ouvrage.nom, ouvrage.exemplaires);
  });
  divDroite.innerHTML += "</ol>";
}

function affiche_exemplaires(ouvrageCode, ouvrageNom, exemplaires) {
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
      let encodedNom = btoa(unescape(encodeURIComponent(ouvrageNom)));
      ul.innerHTML += `<button onclick='addToCart(${ouvrageCode}, "${encodedNom}")'> 🛒 Ajouter </button>`;
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

function addToCart(code_ouvrage, encoded_nom_ouvrage) {
  fetch("panier.php?action=add", {
    method: "POST",
    body: new URLSearchParams({
      code_ouvrage,
      nom_ouvrage: encoded_nom_ouvrage,
    }),
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
    .then((response) => response.json())
    .then((data) => alert(data.message))
    .catch((error) => console.error("Erreur:", error));
}

function removeLivre(code_ouvrage) {
  fetch("panier.php?action=remove", {
    method: "POST",
    body: new URLSearchParams({ code_ouvrage }),
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
    .then((response) => response.json())
    .then((data) => {
      alert(data.message);
      location.reload();
    })
    .catch((error) => console.error("Erreur:", error));
}


function hide_form() {
  document.getElementById("formdiv").style.display = "none";
  document.getElementById("search-div").style.display = "block";
}

function show_form() {
  document.getElementById("form-div").style.display = "block";
  document.getElementById("search-div").style.display = "none";
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
                hide_form();
            } else {
                $("#messageErreur").html(`<p style="color:red;">${data.message}</p>`);
            }
        }
    });
}
