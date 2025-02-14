# BookMyCut

### Choix du projet
**BookMyCut** est une application web développée avec Symfony 7 et PHP 8.3, utilisant Docker pour l'environnement de développement. Elle s'adresse aux salons de coiffure désirant gérer leurs rendez-vous et leur stock de produits simplement. 

### User Stories  
- **US1. En tant que client, je veux créer un compte et m'y authentifier afin de prendre rendez-vous.**
- [ ] Lorsque je suis sur la page "Inscription", quand j'ai rempli le formulaire et que je clique sur le bouton "S'inscrire", alors je peux me connecter à mon compte. 
- [ ] Lorsque je suis sur la page "Connexion", quand j'ai rempli mon email, mon mot de passe et que je clique sur le bouton "Connexion", alors je suis connectée et je peux prendre rendez-vous.
- **US2. En tant que client, je veux avoir des informations sur les prestations afin de choisir ma prestation.**
- [ ] Lorsque je suis sur la page d'accueil, quand je clique sur "Services", alors je peux voir tous les services disponibles. 
- **US3. En tant que client, je veux réserver une prestation afin d'obtenir ma prestation.**
- [ ] Lorsque je suis sur la page "Services" et que j'ai chosi ma prestation, quand je clique sur "Réserver", alors je suis sur la page "Réservations".
- [ ] - [ ] Lorsque je suis sur la page "Réservations", quand je chosis une date, alors les crenaux disponibles sont affiches et je peux choisir une heure. 
- [ ] Lorsque je suis sur la page "Réservations", quand je chosis une heure, alors je peux cliquer sur "Confirmer votre rendez-vous". 
- **Coiffeur :**
- **US4. En tant que coiffeur, je veux m'authentifier à un compte administrateur afin de voir les fonctionnalites admin.**
- [ ] Lorsque je suis sur la page "Connexion", quand j'ai rempli mon email, mon mot de passe et que je clique sur le bouton "Connexion", alors je suis connectée et je peux voir les fonctionnalites admin
- **US5. En tant que coiffeur, je veux voir l'agenda avec les rendez-vous afin de m'organiser.**
- [ ] Lorsque je suis sur la page "Dashboards", quand je clique sur le bouton "Voir les reservations", alors je suis sur la page "Réservations".
- [ ] Lorsque je suis sur la page "Réservations", quand je clique sur le bouton "month", alors je vois mon agenda avec les rendez-vous planifies pour le mois choisi.
- [ ] Lorsque je suis sur la page "Réservations", quand je clique sur le bouton "week", alors je vois mon agenda avec les rendez-vous planifies pour la semaine choisi.
- [ ] Lorsque je suis sur la page "Réservations", quand je clique sur le bouton "day", alors je vois mon agenda avec les rendez-vous planifies pour le jour choisi.
- **US6. En tant que coiffeur, je veux avoir l'information sur mon stock afin de générer une liste de courses.**
- [ ] Lorsque je suis sur la page "Dashboards", quand je clique sur le bouton "Mon stock", alors je suis sur la page "Stock" et je vois mon stock et les produits a commander.
- **US7. En tant que coiffeur, je veux Accès à une estimation du chiffres d'affaires mensuel afin d'av.**
- [ ] Lorsque je suis sur la page "Dashboards", quand je clique sur le bouton "Chiffre d'affaires", alors je suis sur la page "Chiffres d'affaires" et voir mon profit, le nombre de clients et les dernières transactions.


### Schéma de la base de données
![Schéma](schema_BDD.png)
- 1 table utilisateur (client et coiffeur)
- 1 table rendez-vous (date, horaire, prestation)
- 1 table prestation (nom, produit nécessaire, quantité nécessaire)
- 1 table stock (produit, quantité)

### Maquettes avec Figma
https://www.figma.com/design/pfgxXrqYAUlA0grtzf6oOq/Untitled?node-id=0-1&m=dev&t=hiGhsBEL2okKY0sN-1

### Kanban
Trello : https://trello.com/invite/b/679673e0da344137f1ead289/ATTI4f90adfb3a82eb787abb607306fe39bbC7262E66/bookmycut 
