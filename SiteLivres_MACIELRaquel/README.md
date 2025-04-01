# Bibliothèque Virtuelle  

Ce projet est un système de bibliothèque virtuelle permettant aux utilisateurs de rechercher des livres, de consulter les exemplaires disponibles et de gérer un panier d'achat. 

[](demo/demo6.png)
[](demo/demo15.png)

## Prérequis  

Avant d'exécuter le projet, assurez-vous d'avoir installé :  

- **PHP** (>=7.4)  
- **PostgreSQL** 
- **Apache**

## Installation  

1. **Configurer la base de données**  
   - Créez une base de données dans MySQL :
    ```sh
     sudo -u postgres psql
     ```  

     ```sql
     CREATE DATABASE livres;
     ```

   - Importez le schéma de la base de données :  
     ```sh
     psql -U postgres -d livres -f sql/populate.sql
     psql -U postgres -d livres
     ```  

     ```sql
     \i inscription.sql
     \i create_database.sql
     ```  

2. **Lancer le serveur**  
   ```sh
   php -S localhost:8000
   ```  
   Ensuite, accédez au système via : [http://localhost:8000](http://localhost:8000)  

## 📜 Structure du projet  

```
📂 bibliotheque-virtuelle
│-- 📂 demo/               # Demonstration par video, pdf et images
│-- 📂 php/                # Code  en PHP
│-- 📂 sql/               # Fichiers SQL pour la création de la base de données et fonction
│-- 📜 counter.txt         # Fichier pour counter visites
│-- 📜 index.php           # Page principale
│-- 📜 logo.png            # Logo
│-- 📜 README.md           # Ce fichier
│-- 📜 script.js           # Fonctions JS
│-- 📜 style.css           # Style
```  

## 🎯 Fonctionnalités  

- **Recherche par auteur et par titre**  
- **Gestion dynamique du panier**  
- **Affichage des détails des exemplaires**  
- **Système d'inscription**  
- **Interface responsive et ergonomique**  
