# BookMyCut

### Choix du projet
**BookMyCut** est une application web qui s'adresse aux salons de coiffure désirant gérer leurs rendez-vous et leur stock de produits simplement. 

### Technologies utilisées
- Symfony 7
- PHP 8.3
- Docker(-compose) pour l'environnement de développement
- Composer
- PostgreSQL
- Doctrine
- Twig
- Bootstrap (framework CSS)

### Diagramme de classe pour la base de données
![Schéma](schema_bdd.png)

### Installer et exécuter le projet
1. Cloner le dépôt du projet 
```bash
git clone https://github.com/camviweb/bookmycut.git
```

2. Lancer Docker Desktop

3. Construire l'image Docker
```bash
make build
```

4. Démarrer les conteneurs Docker
```bash
make up
```

5. Installer les dépendances PHP avec Composer
```bash
make composer-install
```

6. Exécuter les migrations de la base de données
```bash
make migrate 
```

7. Charger les fixtures 
```bash
docker exec bookmycut_php php bin/console d:f:l --no-interaction
```

8. Ajouter cette ligne "127.0.0.1 bookmycut.local" au fichier hosts

9. Accéder à l'app 'bookmycut.local' ! 

### Maquettes avec Figma
https://www.figma.com/design/pfgxXrqYAUlA0grtzf6oOq/Untitled?node-id=0-1&m=dev&t=hiGhsBEL2okKY0sN-1

### Support de présentation 
https://docs.google.com/presentation/d/1CvXbs92Go8MvBhc3UO_YZ5NAN6xQOpVGplkPFnY5-8M/edit?usp=sharing 

### Kanban
Trello : https://trello.com/invite/b/679673e0da344137f1ead289/ATTI4f90adfb3a82eb787abb607306fe39bbC7262E66/bookmycut 

### User Stories  
- **US1. En tant que client, je veux créer un compte et m'y authentifier afin de prendre rendez-vous.**
- [ ] Lorsque je suis sur la page "Inscription", quand j'ai rempli le formulaire et que je clique sur le bouton "S'inscrire", alors je peux me connecter à mon compte. 
- [ ] Lorsque je suis sur la page "Connexion", quand j'ai rempli mon email et mon mot de passe et que je clique sur le bouton "Connexion", alors je suis connectée et je peux prendre rendez-vous.
- **US2. En tant que client, je veux avoir des informations sur les prestations afin de choisir ma prestation.**
- [ ] Lorsque je suis sur la page d'accueil, quand je clique sur "Services", alors je peux voir tous les services disponibles. 
- **US3. En tant que client, je veux réserver une prestation afin d'obtenir ma prestation.**
- [ ] Lorsque je suis sur la page "Services" et que j'ai chosi ma prestation, quand je clique sur "Réserver", alors je suis sur la page "Réservations".
- [ ] Lorsque je suis sur la page "Réservations", quand je choisis une date, alors les créneaux disponibles sont affichés et je peux choisir une heure. 
- [ ] Lorsque je suis sur la page "Réservations", quand je choisis une heure, alors je peux cliquer sur "Confirmer votre rendez-vous". 
- **US4. En tant que coiffeur, je veux m'authentifier à un compte administrateur afin de voir les fonctionnalités admin.**
- [ ] Lorsque je suis sur la page "Connexion", quand j'ai rempli mon email et mon mot de passe et que je clique sur le bouton "Connexion", alors je suis connectée et je peux voir les fonctionnalités admin.
- **US5. En tant que coiffeur, je veux voir l'agenda avec les rendez-vous afin de m'organiser.**
- [ ] Lorsque je suis sur la page "Dashboard", quand je clique sur le bouton "Voir les réservations", alors je suis sur la page "Réservations".
- [ ] Lorsque je suis sur la page "Réservations", quand je clique sur le bouton "month", alors je vois mon agenda avec les rendez-vous planifiés pour le mois choisi.
- [ ] Lorsque je suis sur la page "Réservations", quand je clique sur le bouton "week", alors je vois mon agenda avec les rendez-vous planifiés pour la semaine choisie.
- [ ] Lorsque je suis sur la page "Réservations", quand je clique sur le bouton "day", alors je vois mon agenda avec les rendez-vous planifiés pour le jour choisi.
- **US6. En tant que coiffeur, je veux voir les informations sur mon stock afin de générer une liste de courses.**
- [ ] Lorsque je suis sur la page "Dashboard", quand je clique sur le bouton "Mon stock", alors je suis sur la page "Stock" et je vois mon stock et les produits à commander.
- **US7. En tant que coiffeur, je veux accéder à une estimation de mon chiffre d'affaires mensuel afin d'avoir des statistiques.**
- [ ] Lorsque je suis sur la page "Dashboards", quand je clique sur le bouton "Chiffre d'affaires", alors je suis sur la page "Chiffres d'affaires" et je vois mon profit, le nombre de clients et les dernières transactions.