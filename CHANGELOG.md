# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

## [Unreleased]

### Ajouté
- `src/Application.php` : classe minimale chargée par l'autoload PSR-4.
- Chargement de `vendor/autoload.php` dans le Front Controller pour vérifier Composer.
- `config/database.php` : configuration centralisée de la connexion MySQL.
- Initialisation de Capsule/Eloquent et chargement de `.env` dans le Front Controller.
- `src/Model/Salle.php` et `src/Model/Reservation.php` : modèles Eloquent et relations
  `hasMany` / `belongsTo`.
- `database/migrations/` et `database/migrate.php` : création idempotente des tables
  `salles` et `reservations`, avec clé étrangère et index de recherche.
- `database/seed.php` : ajout idempotent des cinq salles de démonstration demandées.
- `src/Validation/` : contrat, résultat et validateurs Respect.Validation pour les
  salles et réservations.
- `src/DTO/` : DTO typés pour convertir les données validées des salles et réservations.
- `src/Repository/` : contrats et implémentations Eloquent pour les salles et réservations,
  y compris la recherche de conflits et l'annulation.

## v0.0.0 — Phase 1 : infrastructure Docker

### Ajouté
- `Dockerfile` : image PHP-FPM 8.3 avec extensions `pdo`, `pdo_mysql`, `mbstring`,
  `intl`, `opcache`, et Composer installé.
- `docker-compose.yml` : services `nginx`, `app`, `mysql` reliés par un réseau Docker
  dédié, volume `mysql_data` pour la persistance, healthcheck MySQL.
- `docker/nginx/default.conf` : configuration Nginx servant `public/`, transmission des
  `.php` à `app:9000`, blocage des fichiers sensibles.
- `public/index.php` : page d'accueil vérifiant la chaîne
  Navigateur → Nginx → PHP-FPM → PHP, ainsi que la connexion PHP → MySQL.
- `.env.example`, `.gitignore`, `.dockerignore`.
- `composer.json` : squelette avec autoload PSR-4 `App\` → `src/` (dépendances ajoutées
  en Phase 2).
- `README.md` : prérequis, installation, commandes Docker.
