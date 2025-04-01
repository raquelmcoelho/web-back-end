window.onload = function() {
  affiche_auteurs([]);
  affiche_ouvrages([]);
  mise_a_jour_panier();
  montrer_recherche();
}

function by_id(id) {
  return document.getElementById(id);
}

// Recherches
function recherche_auteurs() {
  let debnom = by_id("debnom").value;
  if(debnom == "") {
    affiche_auteurs([]);
    return null;
  }

  $.ajax({
    type: "GET",
    url: "php/recherche.php?type=auteurs&key=" + encodeURIComponent(debnom),
    dataType: "json",
    success: function (data) {
      affiche_auteurs(data);
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}
function recherche_ouvrages_titre() {
  let debtitre = by_id("debtitre").value;
  if(!debtitre) {
    affiche_ouvrages([]);
    return null;
  }

  $.ajax({
    type: "GET",
    url: "php/recherche.php?type=ouvrages_titre&key=" + encodeURIComponent(debtitre),
    dataType: "json",
    success: function (data) {
      affiche_ouvrages(data);
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

function recherche_ouvrages_auteur(code) {
  $.ajax({
    type: "GET",
    url: "php/recherche.php?type=ouvrages_auteur&key=" + code,
    dataType: "json",
    success: function (data) {
      affiche_ouvrages(data);
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

// Affichage
function affiche_auteurs(auteurs) {
  let divGauche = by_id("div-gauche");
  let html = `<table class="table-class">
                <thead>
                  <tr><th>Auteur</th></tr>
                </thead>
                <tbody>`;
  
  auteurs.forEach((auteur) => {
    html += `<tr><td><a href="#" onclick="recherche_ouvrages_auteur(${auteur.code})">${auteur.nom} ${auteur.prenom}</a></td></tr>`;
  });

  html += `</tbody></table>`;
  divGauche.innerHTML = html;
}

function affiche_ouvrages(ouvrages) {
  let divDroite = by_id("div-droite");
  divDroite.innerHTML = `<table class="table-class">
                <thead>
                  <tr><th>Ouvrage</th></tr>
                </thead>
                <tbody>`;

  ouvrages.forEach((ouvrage) => {
    divDroite.innerHTML += `<tr><td><h4>${ouvrage.nom}</h4></td></tr>
             <tr><td><table class="sub-table" id="table-${ouvrage.code}"></table></td></tr>`;
    affiche_exemplaires(ouvrage.code, ouvrage.exemplaires);
  });

  divDroite.innerHTML += `</tbody></table>`;
}

function affiche_exemplaires(ouvrageCode, exemplaires) {
  let table = by_id(`table-${ouvrageCode}`);
  exemplaires = JSON.parse(exemplaires);
  let html="";

  if (exemplaires.length === 0) {
    html += `<tr><td colspan="3">Aucun exemplaire disponible</td></tr>`;
  } else {
    exemplaires.forEach((exemplaire) => {
      html += `<tr>
                 <td>${exemplaire.editeur}</td>
                 <td>${exemplaire.prix !== null ? `${exemplaire.prix} €` : "Indisponible"}</td>
                 <td>${exemplaire.prix !== null ? `<button onclick='ajouter_livre(${exemplaire.code})'>🛒 Ajouter</button>` : ""}</td>
               </tr>`;
    });
  }

  table.innerHTML = html;
}

function afficher_panier(panier) {
  let panierDiv = document.getElementById("panier-div");
  let prixTotal = 0.0;

  if (panier.length === 0) {
    panierDiv.innerHTML = "<p>Votre panier est vide.</p>";
  } else {
    let html = `<h1>Votre Panier</h1>
                <table class="table-class">
                  <thead>
                    <tr><th>Nom</th><th>Éditeur</th><th>Quantité</th><th>Prix</th><th>Action</th></tr>
                  </thead>
                  <tbody>`;

    panier.forEach(item => {
      prixTotal += parseFloat(item.prix);
      html += `<tr>
                 <td>${item.nom}</td>
                 <td>${item.editeur}</td>
                 <td>${item.quantite}</td>
                 <td>${item.prix} €</td>
                 <td><button class="cancel" onclick="retirer_livre(${item.code_exemplaire})">Supprimer</button></td>
               </tr>`;
    });

    html += `<tr><td colspan="3"><strong>Total</strong></td><td><strong>${prixTotal.toFixed(2)} €</strong></td><td></td></tr>`;
    html += `</tbody></table>`;
    
    panierDiv.innerHTML = html;
  }

  panierDiv.innerHTML += `<br>`;
  panierDiv.innerHTML += `<button class="cancel" type='button' onclick='montrer_recherche()'>Fermer</button>`;
  panierDiv.innerHTML += `<button class="confirm" type='button' onclick='commander()'>Commander</button>`;
}

// Panier 
function ajouter_livre(code_exemplaire) {
  $.ajax({
    type: 'POST',
    url: 'php/panier.php?action=ajouter',
    data: { code_exemplaire },
    dataType: 'json',
    success: function(data) {
      if (data.success) {
        alert(data.message);
        mise_a_jour_panier();
      } else {
        alert("Il faut s'inscrire pour ajouter un livre au panier.");
        montrer_formulaire();
      }
    },
    error: function(xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

function retirer_livre(code_exemplaire) {
  $.ajax({
    type: "POST",
    url: "php/panier.php?action=retirer",
    data: { code_exemplaire },
    dataType: "json",
    success: function (data) {
      if (data.success) {
        alert(data.message);
        mise_a_jour_panier();
      } else {
        alert("Erreur lors de la suppression du livre du panier.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

function vider_panier() {
  $.ajax({
    type: "POST",
    url: "php/panier.php?action=vider",
    success: function (data) {
      if (data.success) {
        alert(data.message);
        mise_a_jour_panier();
      } else {
        alert("Erreur lors de la vidange du panier.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

function mise_a_jour_panier() {
  let code_client = get_cookie("code_client");
  if (!code_client) {
    return;
  }

  $.ajax({
    type: "GET",
    url: "php/panier.php?action=recuperer",
    dataType: "json",
    success: function (data) {
      if (data.success) {
        afficher_panier(data.panier);
      } else {
        alert("Erreur lors de la récupération du panier.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

// source: https://stackoverflow.com/questions/10730362/get-cookie-by-name
function get_cookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

function commander() {
  let code_client = get_cookie("code_client");
  if (code_client) {
    $.ajax({
      type: "POST",
      url: "php/panier.php?action=commander",
      data: { code_client },
      dataType: "json",
      success: function (data) {
        alert(data.message);
        mise_a_jour_panier();
      },
      error: function (xhr, status, error) {
        console.error("Erreur:", error);
      }
    });
  } else {
    alert("Vous devez vous inscrire pour commander.");
    montrer_formulaire();
  }
}


// Tabs hide/show
function montrer_panier() {
  mise_a_jour_panier();
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



// Login / Logout
// source: https://www.sitepoint.com/delay-sleep-pause-wait/
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

console.log('Hello');
sleep(2000).then(() => { console.log('World!'); });
function enregistrement() {
    $.ajax({
        type: "POST",
        url: "php/inscription.php",
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
                location.reload();
                montrer_recherche();
            } else {
                $("#message_erreur").html(`<p style="color:red;">${data.message}</p>`);
                sleep(2000).then(() => {
                    $("#message_erreur").html("");
                });
            } 
        }
    });
}

function deconnecter() {
  $.ajax({
    type: "GET",
    url: "php/deconnexion.php",
    success: function(data) {
      location.reload();
    }
  });
}
