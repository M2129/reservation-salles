# Gestion des réservations de salles universitaires

Application web permettant de consulter les salles d'une université et de gérer leurs
réservations, en remplacement du système actuel par courriel (source de doublons).

Projet pédagogique (ODC Sonatel Academy) développé en PHP orienté objet **sans framework
complet**, avec des composants spécialisés : FastRoute, Respect\Validation, Eloquent
(`illuminate/database`), PHP-DI.

> Ce projet avance par phases. Ce README documente l'état actuel de la **Phase 13**
> (PHP-DI et injection de dépendances).

## Prérequis

Seuls ces outils sont nécessaires sur la machine hôte :

- Docker
- Docker Compose
- Git

PHP, Composer, MySQL et Nginx ne sont **pas** installés directement sur la machine :
tout s'exécute dans des conteneurs.

## Installation

```bash
git clone <url-du-depot>
cd reservation-salles
cp .env.example .env
docker compose build
docker compose up -d
```

Vérifier que les trois services tournent :

```bash
docker compose ps
```

Vous devez voir : `nginx`, `app`, `mysql`.

L'application est ensuite accessible sur :

```
http://localhost:8080
```

La page d'accueil affiche le titre du projet et l'état de la connexion PHP → MySQL.

## Architecture Docker

```
Navigateur
    │ HTTP :8080
    ▼
  Nginx  (port 80 dans le conteneur)
    │ FastCGI
    ▼
 PHP-FPM (service "app", PHP 8.3+, Composer)
    │ TCP :3306
    ▼
  MySQL 8.4 (volume "mysql_data")
```

- Nginx ne communique **qu'avec** PHP-FPM (`app:9000`), jamais `localhost`.
- PHP-FPM ne communique **qu'avec** MySQL (`mysql:3306`), jamais `localhost`.
- La racine publique servie par Nginx est `public/`.
- Le point d'entrée unique de l'application est `public/index.php`.

## Commandes utiles

```bash
docker compose up -d           # démarrer les services en arrière-plan
docker compose down            # arrêter les services (les données MySQL persistent)
docker compose down -v         # arrêter ET supprimer le volume MySQL (destructif)
docker compose restart         # redémarrer les services
docker compose ps              # lister l'état des services
docker compose logs            # logs de tous les services
docker compose logs -f         # suivre les logs en direct
docker compose logs nginx
docker compose logs app
docker compose logs mysql
```

Composer (à partir de la Phase 2, une fois des dépendances déclarées) :

```bash
docker compose exec app composer install
docker compose exec app composer dump-autoload
docker compose exec app php database/migrate.php
docker compose exec app php database/seed.php
```

La connexion Eloquent est initialisée une seule fois par le Front Controller :
`.env` est chargé par `vlucas/phpdotenv`, puis `config/database.php` fournit la
configuration à `Illuminate\Database\Capsule\Manager`. Les classes métier ne
lisent pas directement les variables d'environnement.

PHP :

```bash
docker compose exec app php -v
```

Connexion directe à MySQL :

```bash
docker compose exec mysql mysql -uapp -p university_rooms
```

## Persistance des données

Les données MySQL sont stockées dans le volume Docker nommé `mysql_data`. Elles
survivent à un `docker compose down`. Seule la commande explicitement destructive
`docker compose down -v` supprime ce volume et donc les données.

## Déploiement sur Render

Le service web Render doit utiliser le runtime **Docker** avec :

- Dockerfile : `Dockerfile.render`
- Docker context : `.`
- Health check path : `/`

`Dockerfile.render` lance Nginx et PHP-FPM dans le même conteneur. Nginx écoute
automatiquement le port fourni par Render via la variable `$PORT`.

Render ne fournit pas de service MySQL intégré pour ce projet. Il faut donc
utiliser un fournisseur MySQL externe et renseigner dans les variables
d'environnement Render :

```text
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=<hote-mysql-externe>
DB_PORT=3306
DB_DATABASE=<nom-de-la-base>
DB_USERNAME=<utilisateur>
DB_PASSWORD=<mot-de-passe>
```

Ne pas mettre ces valeurs dans GitHub. Le fichier `render.yaml` prépare les
variables non secrètes et laisse les secrets à saisir dans le tableau de bord
Render. Après le premier déploiement, exécuter les migrations et le seed depuis
un shell de déploiement ou une tâche ponctuelle :

```bash
php database/migrate.php
php database/seed.php
```

## État du projet

- [x] Phase 1 — Docker + Nginx + PHP-FPM + MySQL
- [x] Phase 2 — Composer + PSR-4 + dépendances
- [x] Phase 3 — Eloquent + connexion MySQL
- [x] Phase 4 — Models + relations
- [x] Phase 5 — Migrations
- [x] Phase 6 — Seed
- [x] Phase 7 — Validation
- [x] Phase 8 — DTO
- [x] Phase 9 — Repositories
- [x] Phase 10 — Services + règles métier
- [x] Phase 11 — Controllers + Views
- [x] Phase 12 — FastRoute
- [x] Phase 13 — PHP-DI
- [ ] Phase 14 — Tests
- [ ] Phase 15 — Sécurité + finition
- [ ] Phase 16 — README + ARCHITECTURE.md complets
- [ ] Phase 17 — Tests finaux + Docker → v1.0.0
# reservation-salles
