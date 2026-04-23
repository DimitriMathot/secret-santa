# 🐳 Docker Setup

## Prérequis

- Docker
- Docker Compose

## Démarrage

1. **Copier le fichier d'environnement** :
   ```bash
   cp .env.example .env
   ```

2. **Configurer le `.env`** avec tes paramètres (base de données, Mailgun, etc.)

   Configuration minimale recommandée avec Docker :
   ```env
   APP_URL=http://localhost:8081

   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=secret_santa
   DB_USERNAME=secret_santa
   DB_PASSWORD=secret

   QUEUE_CONNECTION=database
   ```

3. **Lancer les conteneurs** :
   ```bash
   docker compose up -d --build
   ```

4. **Générer la clé d'application** :
   ```bash
   docker compose exec app php artisan key:generate
   ```

5. **Lancer les migrations** :
   ```bash
   docker compose exec app php artisan migrate
   ```

6. **Accéder à l'application** :
   - Application : http://localhost:8081
   - Base de données : localhost:3307

## Commandes utiles

**Voir les logs** :
```bash
docker compose logs -f
```

**Exécuter des commandes Artisan** :
```bash
docker compose exec app php artisan [commande]
```

**Accéder au shell du conteneur** :
```bash
docker compose exec app bash
```

**Arrêter les conteneurs** :
```bash
docker compose down
```

**Rebuild les conteneurs** :
```bash
docker compose up -d --build
```

## Structure

- `app` : Conteneur PHP-FPM avec Laravel
- `nginx` : Serveur web Nginx
- `queue` : Worker Laravel pour les emails et jobs en arrière-plan
- `db` : Base de données MySQL

## Configuration de la base de données

Par défaut, la base de données est configurée avec :
- Host: `db`
- Database: `secret_santa`
- User: `secret_santa`
- Password: `secret`

Modifie ces valeurs dans `docker-compose.yml` et `.env` si nécessaire.
