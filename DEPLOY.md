# 🚀 Guide de déploiement automatique sur VPS

## 📋 Prérequis sur le VPS

### 1. Installer Docker et Docker Compose
```bash
# Installer Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Installer Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Vérifier l'installation
docker --version
docker compose version
```

### 2. Installer Git
```bash
sudo apt update
sudo apt install git -y
```

### 3. Créer la structure de dossiers
```bash
sudo mkdir -p /var/www/secret-santa
sudo chown -R $USER:$USER /var/www/secret-santa
```

### 4. Cloner le repository (première fois)
```bash
cd /var/www/secret-santa
git clone https://github.com/TON_USERNAME/secret-santa.git .
# Ou avec SSH si tu as configuré les clés
# git clone git@github.com:TON_USERNAME/secret-santa.git .
```

### 5. Configurer le fichier .env
```bash
cd /var/www/secret-santa
cp .env.example .env
nano .env  # ou vi .env
```

**Variables importantes à configurer :**
```env
APP_NAME="Secret Santa"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ton-sous-domaine.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=secret_santa
DB_USERNAME=secret_santa
DB_PASSWORD=secret

QUEUE_CONNECTION=database

MAIL_MAILER=mailgun
MAILGUN_DOMAIN=ton-domaine.mailgun.org
MAILGUN_SECRET=ton_api_key
MAIL_FROM_ADDRESS=noreply@ton-domaine.com
MAIL_FROM_NAME="Secret Santa"
```

### 6. Générer la clé d'application
```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

## 🔐 Configuration GitHub Secrets

Dans ton repository GitHub, va dans **Settings** → **Secrets and variables** → **Actions** et ajoute :

1. **VPS_HOST** : L'IP ou le domaine de ton VPS (ex: `123.456.789.0` ou `vps.tondomaine.com`)
2. **VPS_USER** : Ton utilisateur SSH (ex: `root` ou `ubuntu`)
3. **VPS_SSH_KEY** : Ta clé SSH privée complète (commence par `-----BEGIN OPENSSH PRIVATE KEY-----`)

**Pour générer une clé SSH si tu n'en as pas :**
```bash
ssh-keygen -t ed25519 -C "github-deploy"
# Copie la clé privée (~/.ssh/id_ed25519) dans VPS_SSH_KEY
# Et ajoute la clé publique (~/.ssh/id_ed25519.pub) dans ~/.ssh/authorized_keys sur ton VPS
```

## 🌐 Configuration Nginx (reverse proxy)

Si tu veux utiliser un sous-domaine avec HTTPS, configure Nginx sur ton VPS :

```nginx
server {
    listen 80;
    server_name secret-santa.tondomaine.com;

    location / {
        proxy_pass http://localhost:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Puis installe Certbot pour HTTPS :
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d secret-santa.tondomaine.com
```

## ✅ Vérification

Une fois tout configuré :

1. **Push sur la branche `main`** → Le workflow se déclenche automatiquement
2. **Vérifie les logs GitHub Actions** pour voir si le déploiement fonctionne
3. **Accède à ton site** pour vérifier que tout fonctionne

## 🔧 Commandes utiles sur le VPS

**Voir les logs des conteneurs :**
```bash
cd /var/www/secret-santa
docker compose logs -f
```

**Redémarrer les conteneurs :**
```bash
docker compose restart
```

**Voir les conteneurs en cours :**
```bash
docker compose ps
```

**Exécuter une commande Artisan :**
```bash
docker compose exec app php artisan [commande]
```

## 🐛 Dépannage

**Le workflow échoue :**
- Vérifie que les secrets GitHub sont bien configurés
- Vérifie que la clé SSH est bien ajoutée dans `~/.ssh/authorized_keys` sur le VPS
- Vérifie que le chemin `/var/www/secret-santa` existe et contient le repo

**Les migrations échouent :**
- Vérifie que la base de données est bien démarrée : `docker compose ps`
- Vérifie les logs : `docker compose logs db`

**L'application ne répond pas :**
- Vérifie que les conteneurs sont démarrés : `docker compose ps`
- Vérifie les logs : `docker compose logs -f`
- Vérifie que le port 80 n'est pas déjà utilisé
