![separe](https://github.com/studoo-app/.github/blob/main/profile/studoo-banner-logo.png)
# Symfony TP 3 - Modélisation avec l'ORM Doctrine
[![Version](https://img.shields.io/badge/Version-2025-blue)]()
[![Niveau](https://img.shields.io/badge/Niveau-SIO2-yellow)]()

## 🎯 Contexte professionnel

Vous travaillez pour **EduTech Solutions**, une startup spécialisée dans les plateformes d'apprentissage en ligne. L'entreprise souhaite développer une nouvelle plateforme permettant de gérer des formations, des apprenants, et des instructeurs.

Votre équipe a été chargée de créer le **module de gestion des données** de cette plateforme. Le système doit permettre de :
- Gérer les formations avec leurs modules et leçons
- Inscrire des apprenants aux formations
- Assigner des instructeurs aux formations
- Suivre les progrès des apprenants

Votre mission : modéliser et implémenter la couche de données avec Doctrine ORM.

## 📋 Objectifs pédagogiques

**Compétences techniques visées :**
- Installer et configurer Doctrine ORM dans un projet Symfony
- Concevoir et créer des entités avec leurs propriétés et contraintes
- Implémenter les relations entre entités (OneToMany, ManyToOne, ManyToMany)
- Utiliser le système de migrations pour faire évoluer la base de données
- Gérer le cycle de vie des entités avec l'EntityManager

**Compétences transversales :**
- Analyser un cahier des charges pour en extraire un modèle de données
- Structurer et organiser son code selon les bonnes pratiques
- Documenter ses choix techniques et respecter les contraintes réglementaires

## 🛠️ Consignes détaillées

### 🚀 Phase 1 : Installation et Configuration de Doctrine (60 minutes)

#### Étape 1.1 : Préparation du projet
Créez un nouveau projet Symfony nommé `formation-platform` et installez les dépendances nécessaires :

```bash
# Créer le projet
symfony new formation-platform --webapp
```

#### Étape 1.2 : Configuration de la base de données
Configurez la stack docker afin d'avoir un container de base de données et un container PHPMYAdmin, mettez à jour le fichier `compose.yaml`:

```yaml
services:  
  database:  
    container_name: tp3-database  
    image: mariadb:10.11.2  
    ports:  
      - "3306:3306"  
    restart: always  
    environment:  
      MYSQL_DATABASE: app_db  
      MYSQL_ALLOW_EMPTY_PASSWORD: 'no'  
      MYSQL_ROOT_PASSWORD: root  
    volumes:  
      - ./var/dbdata:/var/lib/mysql  
    networks:  
      - tp3_network  
  phpmyadmin:  
    container_name: tp3-pma  
    image: phpmyadmin/phpmyadmin  
    ports:  
      - "8081:80"  
    environment:  
      PMA_HOST: database  
      PMA_PORT: 3306  
      PMA_ARBITRARY: 1  
      UPLOAD_LIMIT: 1G  
      MEMORY_LIMIT: 512M  
      MAX_EXECUTION_TIME: 0  
    restart: always  
    links:  
      - database  
    networks:  
      - tp3_network  
networks:  
  tp3_network:
```

Configurez votre connexion à la base de données dans le fichier `.env` :

```bash
# Exemple pour MySQL/MariaDB
DATABASE_URL="mysql://root:root@127.0.0.1:3306/app_db?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

Démarrez les containers:

```bash
docker compose up -d
```

Créez la base de données :
```bash
symfony console doctrine:database:create
```

**Vérification :** Assurez-vous que la base de données est créée sans erreur.

### 📊 Phase 2 : Modélisation des Entités de Base

#### Étape 2.1 : Entité Utilisateur (Base pour tous les acteurs)
Créez une entité `Utilisateur` qui servira de base pour les apprenants et instructeurs :

**Propriétés requises :**
- `id` : identifiant unique auto-généré
- `email` : adresse email unique, obligatoire
- `nomAffichage` : nom d'affichage public, 100 caractères max
- `motDePasse` : mot de passe hashé
- `dateInscription` : date d'inscription automatique
- `estActif` : booléen pour activer/désactiver le compte

**Contraintes de validation :**
- Email valide et unique
- Nom d'affichage : minimum 2 caractères, maximum 100

```bash
symfony console make:user Utilisateur
```

#### Étape 2.2 : Entité Formation
Créez l'entité `Formation` représentant une formation proposée :

**Propriétés requises :**
- `id` : identifiant unique
- `titre` : titre de la formation, 200 caractères max
- `description` : description détaillée (text)
- `dureeHeures` : durée en heures (entier)
- `niveauDifficulte` : niveau (string : 'Débutant', 'Intermédiaire', 'Avancé')
- `prix` : prix en euros (decimal avec 2 décimales)
- `dateCreation` : date de création automatique
- `estPubliee` : booléen pour publier/dépublier
- `capaciteMax` : nombre maximum d'apprenants (nullable)

**Contraintes de validation :**
- Titre : obligatoire, 10-200 caractères
- Durée : minimum 1 heure
- Prix : positif ou zéro
- Niveau : doit être une des trois valeurs autorisées

```bash
symfony console make:entity Formation
```

#### Étape 2.3 : Entité Module
Créez l'entité `Module` représentant un module de formation :

**Propriétés requises :**
- `id` : identifiant unique
- `titre` : titre du module, 150 caractères max
- `description` : description du module
- `ordre` : position du module dans la formation (entier)
- `dureeEstimee` : durée estimée en minutes

```bash
symfony console make:entity Module
```

### 🔗 Phase 3 : Relations entre Entités

#### Étape 3.1 : Relation Formation ↔ Modules (OneToMany/ManyToOne)
Implémentez la relation entre `Formation` et `Module` :
- Une formation contient plusieurs modules
- Un module appartient à une seule formation
- La suppression d'une formation doit supprimer ses modules

**Mission autonome :** Implémentez cette relation bidirectionnelle avec les méthodes d'ajout/suppression appropriées.

#### Étape 3.2 : Relation Formation ↔ Instructeur (ManyToMany)
Implémentez la relation entre `Formation` et `Utilisateur` (instructeurs) :
- Un instructeur peut enseigner plusieurs formations
- Une formation peut avoir plusieurs instructeurs
- Utilisez une table de liaison simple

**Conseils :**
- Ajoutez une méthode `getFormationsEnseignees()` à l'entité Utilisateur
- Ajoutez une méthode `getInstructeurs()` à l'entité Formation

#### Étape 3.3 : Entité Inscription (Relation avec données supplémentaires)
Créez une entité `Inscription` pour gérer les inscriptions des apprenants :

**Propriétés requises :**
- `id` : identifiant unique
- `apprenant` : relation vers Utilisateur (ManyToOne)
- `formation` : relation vers Formation (ManyToOne)
- `dateInscription` : date d'inscription automatique
- `statut` : statut ('En cours', 'Terminée', 'Abandonnée')
- `progressionPourcentage` : progression de 0 à 100 (entier)
- `dateFin` : date de fin (nullable)
- `note` : note finale sur 20 (nullable, decimal)


### 📝 Phase 4 : Migrations et Cycles de Vie

#### Étape 4.1 : Génération et exécution des migrations
Générez les migrations pour vos entités :

```bash
symfony console make:migration
```

**Vérification :** Examinez le fichier de migration généré et assurez-vous qu'il correspond à votre modélisation.

Exécutez les migrations :
```bash
symfony console doctrine:migrations:migrate
```

#### Étape 4.2 : Test du cycle de vie des entités
Créez un contrôleur de test `TestController` avec les routes suivantes :

**Route `/test/creation-donnees`** : Créez et persistez :
- 2 utilisateurs (1 instructeur, 1 apprenant)
- 1 formation avec 2 modules
- 1 inscription de l'apprenant à la formation
- Assignez l'instructeur à la formation

**Route `/test/affichage-donnees`** : Affichez :
- La liste des formations avec leurs instructeurs
- Les inscriptions avec les informations des apprenants
- Les modules de chaque formation

**Mission autonome :** Implémentez ces contrôleurs en utilisant l'EntityManager pour tester votre modélisation.

#### Étape 4.3 : Créer un jeu d'essai avec FakerPHP
Installer le package `fakerphp/faker`, puis créer les fichiers de fixtures.
[Documentation]( https://fakerphp.org/)

**Mission autonome :** Créer un jeu d'essai complet et éxécuter la commande de chargement des fixtures.

### 🎨 Phase 5 : Interface de Gestion

#### Étape 5.1 : Pages de consultation
Créez un contrôleur `FormationController` avec :

**Route `/formations`** : Liste toutes les formations publiées avec :
- Titre, description courte, durée, prix
- Nombre d'inscrits / capacité maximale
- Noms des instructeurs

**Route `/formation/{id}`** : Détail d'une formation avec :
- Toutes les informations de la formation
- Liste des modules avec leur ordre
- Instructeurs assignés
- Bouton d'inscription (simulé)

**Mission créative :** Utilisez Bootstrap pour créer une interface claire et professionnelle.

## 📖 Ressources utiles

### Documentation officielle
- [Doctrine ORM Documentation](https://www.doctrine-project.org/projects/orm.html)
- [Doctrine dans Symfony](https://symfony.com/doc/current/doctrine.html)
- [Migrations Doctrine](https://symfony.com/doc/current/doctrine/migrations.html)
- [Validation Symfony](https://symfony.com/doc/current/validation.html)
