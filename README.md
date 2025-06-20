
# Galaxy Swiss Bourdin (GSB) 
Application de gestion de frais, pour des visiteurs médicaux et des comptables.

## Fonctionnalités
Toutes les tâches de la FEB, ont été accomplis : 
- Validation d'une fiche de frais.
- Suivi du paiment des fiches de frais
- Production de la documentation
- Gestion du refus
- Sécurisation des mots de passes
- Gestion kilomètrique
- Génération de PDF 


## Installation
Installation avec composer et PHP
- Changer les informations de connection dans le /config/bdd.php
- Installer les packets avec composer


```bash
  composer install 
```
    
- Lancer le scripts SQL, pour remplir la base de donnée.
```bash
  mysql -u root -P gsb_frais < /resources/bdd/gsb_restore.sql
  mysql -u root -P gsb_frais < /resources/bdd/avant_release.sql
```

- Lancer le script PHP pour hasher les mots de passes
```bash
  php /bin/hashpwd/hash_pwd.php
```

## Utilisation non-prod
```bash
  php -S localhost:8000 -t public
```











## Créateurs
- [@Lemufi](https://www.github.com/Lemufi)
- [@YousraElYebdri](https://github.com/YousraElYebdri)
- [@BaptEds](https://www.github.com/bapteds)

