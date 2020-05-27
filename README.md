# Timer
## Projet Timer - A web application to log passed time by project.

Application web permettant de recenser le temps passé à travailler sur un projet.
Dans le cadre d'un projet de group en autonomie.

Sujet : https://gist.github.com/capywebformation/5298abc51cb60f087a142e2c843ed740

Projet proposé par **Hiren PATEL**.

Réalisé par :
- RUFFINEL Josué
- YIU Léo
- LEAGAUD Inès
- Wu Chin Hung

### Installation 
- Copier le fichier **.env-example** dans un nouveau fichier **.env** 
- Configurer le fichier **.env**
- Lancer la commande pour installer les services  
    `make build`
- Mettre a jour Composer
    `composer update`
- Lancer la commande permettant de se connecter au container  
    `make connect`
- Mettre a jour la base de donnée  
    `php bin/console make:migration`  
    `php bin/console doctrine:migration:migrate`

### Outils & Technologies
Le projet a été réalisé avec les outils & Technologies suivantes :

- HTML/CSS
- PHP/Symfony 5
- JQuery
- MySQL
- Docker
- Git
- Bootstrap

### Commandes Docker
Des commandes ont été enregistrer dans le Makefile.

- Installer et Lancer les services  
    `make build`
      
- Lancer les services  
    `make run`

- Arrêter les services  
    `make down`
    
- Se connecter au container  
    `make connect`
    
### Gestion GIT
Voici quelque regles pour la gestion du GIT pour les développeurs.
- Toutes les branches doivent partir de la branche **dev**.
- Les noms de branches doit correspondre au nom de la fonctionnalité qui sera developpé.
- Les noms de branches devront commencer par **feat/**.
- Les merges requests seront destinée vers la branche **dev** 