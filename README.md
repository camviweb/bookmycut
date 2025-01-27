# BookMyCut

### Choix du projet
**BookMyCut** est une application web développée avec Symfony 7 et PHP 8.3, utilisant Docker pour l'environnement de développement. Elle s'adresse aux salons de coiffure désirant gérer leurs rendez-vous et leur stock de produits simplement. 

### Les fonctionnalités
Client : 
- Création et authentification à un compte
- Prise de rendez-vous selon plusieurs critères (CRUD)
\nCoiffeur :
- Création et authentification à un compte administrateur
- Accès à un agenda avec les rendez-vous (CRUD)
- Gestion du stock avec génération d'une liste de courses (coupe homme : 1/3 shampoing, coupe femme : 1/2 shampoing)
- Accès à une estimation du chiffre d'affaires mensuel

### Schéma de la base de données
- 1 table utilisateur (client et coiffeur)
- 1 table rendez-vous (date, horaire, prestation)
- 1 table prestation (nom, produit nécessaire, quantité nécessaire)
- 1 table stock (produit, quantité)

### Maquettes avec Figma
https://www.figma.com/design/pfgxXrqYAUlA0grtzf6oOq/Untitled?node-id=0-1&m=dev&t=hiGhsBEL2okKY0sN-1

### Choix de l'outil de gestion de projet
Trello : https://trello.com/invite/b/679673e0da344137f1ead289/ATTI4f90adfb3a82eb787abb607306fe39bbC7262E66/bookmycut 
