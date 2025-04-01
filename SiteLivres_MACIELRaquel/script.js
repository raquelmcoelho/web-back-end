function by_id(id) {
  return document.getElementById(id);
}

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

function affiche_auteurs(auteurs) {
  let divGauche = by_id("div-gauche");
  divGauche.innerHTML = "<ol>";
  auteurs.forEach((auteur) => {
    divGauche.innerHTML += `<li><a href="#" onclick="recherche_ouvrages_auteur(${auteur.code})">${auteur.nom} ${auteur.prenom}</a></li>`;
  });
  divGauche.innerHTML += "</ol>";
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

function affiche_ouvrages(ouvrages) {
  let divDroite = by_id("div-droite");
  divDroite.innerHTML = "<ol>";
  ouvrages.forEach((ouvrage) => {
    divDroite.innerHTML += `<li>${ouvrage.nom}<ul id="ul-${ouvrage.code}"></ul></li>`;
    affiche_exemplaires(ouvrage.code, ouvrage.exemplaires);
  });
  divDroite.innerHTML += "</ol>";
}

function affiche_exemplaires(ouvrageCode, exemplaires) {
  let ul = by_id(`ul-${ouvrageCode}`);
  exemplaires = JSON.parse(exemplaires);
  if (exemplaires.length === 0) {
    ul.innerHTML += `<li>Aucun exemplaire disponible</li>`;
  }
  if (exemplaires.length > 0) {
    ul.innerHTML += `<li>Exemplaires disponibles:</li>`;
  }
  exemplaires.forEach((exemplaire) => {
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

function ajouter_panier(code_exemplaire) {
  $.ajax({
    type: 'POST',
    url: 'php/panier.php?action=ajouter',
    data: { code_exemplaire },
    dataType: 'json',
    success: function(data) {
      alert(data.message);
      mise_a_jour_panier();
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
      alert(data.message);
      mise_a_jour_panier();
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
      alert(data.message);
      mise_a_jour_panier();
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

function mise_a_jour_panier() {
  $.ajax({
    type: "GET",
    url: "php/panier.php?action=recuperer",
    dataType: "json",
    success: function (data) {
        afficher_panier(data);
    },
    error: function (xhr, status, error) {
      console.error("Erreur:", error);
    }
  });
}

function afficher_panier(panier) {
  let panierDiv = document.getElementById("panier-div");
  if (panier.length === 0) {
      panierDiv.innerHTML = "<p>Votre panier est vide.</p>";
  } else {
      let html = " <h1>Votre Panier</h1><ul>";
      let prix = 0.0;

      panier.forEach(item => {
          prix += parseFloat(item.prix);
          html += `<li>${item.nom} - ${item.editeur} (Quantité: ${item.quantite}) (Prix: <?= htmlspecialchars(${item.prix}) ?> €)
                   <button onclick="retirer_livre(${item.code_exemplaire})">Supprimer</button></li>`;
      });

      html += `<li><strong>Total: ${prix.toFixed(2)} €</strong></li>`;
      html += "</ul>";
      panierDiv.innerHTML = html;
  }

  panierDiv.innerHTML += "<button type='button' onclick='montrer_recherche()'>Fermer</button>";
  panierDiv.innerHTML += `<button type='button' onclick='commander()'>Commander</button>`;
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
                $("#message-erreur").html(`<p style="color:red;">${data.message}</p>`);
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
