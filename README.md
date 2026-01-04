# Backoffice Project

## Description

Ce projet est un backoffice web développé avec Symfony 7.4, permettant la gestion des Utilisateurs, Produits et Clients.

## Installation

1.  Cloner le dépôt.
2.  Configurer la base de données dans `.env.local` :
    ```bash
    DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
    ```
3.  Installer les dépendances :
    ```bash
    composer install
    ```
4.  Compiler les assets Tailwind CSS :
    ```bash
    php bin/console tailwind:build
    ```
5.  Créer la base de données et charger les données de test (Fixtures) :
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load
    ```

## Fonctionnalités

### Authentification
- Connexion via `/login`.
- Rôles : ADMIN, MANAGER, USER.

### Gestion des Utilisateurs (Admin)
- Liste, Ajout, Modification, Suppression.
- Accès restreint aux administrateurs.

### Gestion des Produits
- Liste visible par tous les utilisateurs connectés.
- Tri par prix décroissant.
- Export CSV.
- **Formulaire Multi-étapes** pour l'ajout/modification (Admin uniquement) :
    - Étape 1 : Type (Physique/Numérique).
    - Étape 2 : Détails.
    - Étape 3 : Logistique ou Licence.
    - Étape 4 : Confirmation (si prix élevé).
- Import CSV via commande CLI : `php bin/console app:import-products products.csv`

### Gestion des Clients (Admin/Manager)
- Liste, Ajout, Modification.
- Commande CLI pour création interactive : `php bin/console app:create-client`.

## Tests

Exécuter les tests unitaires :
```bash
php bin/phpunit
```
