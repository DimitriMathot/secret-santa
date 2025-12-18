# 🎅 Secret Santa Application

Application web privée de Secret Santa construite avec Laravel 11, Blade et Alpine.js.

## ✨ Fonctionnalités

- ✅ **Pas de comptes utilisateur** - Accès direct pour l'admin
- ✅ **Accès basé sur tokens** - Chaque participant reçoit un token unique
- ✅ **Gestion des exclusions** - Les participants peuvent exclure d'autres personnes
- ✅ **Assignations cryptées** - L'admin ne peut jamais voir les assignations
- ✅ **Notifications email** - Les participants reçoivent un email avec leur lien unique
- ✅ **UI en français** - Interface entièrement en français
- ✅ **Architecture propre** - Code organisé avec services et tests

## 🚀 Installation

1. **Installer les dépendances**
   ```bash
   composer install
   npm install
   ```

2. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configurer la base de données**
   - Modifier `.env` avec vos paramètres de base de données
   - Par défaut, SQLite est utilisé : `database/database.sqlite`

4. **Lancer les migrations**
   ```bash
   php artisan migrate
   ```

5. **Compiler les assets** (pour la production)
   ```bash
   npm run build
   ```

   Ou en mode développement :
   ```bash
   npm run dev
   ```

## 📧 Configuration Email

Dans votre fichier `.env`, configurez l'envoi d'emails :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Secret Santa"
```

Pour le développement local, vous pouvez utiliser `MAIL_MAILER=log` pour voir les emails dans `storage/logs/laravel.log`.

## 🎯 Utilisation

1. **Créer un événement**
   - Aller sur la page d'accueil
   - Cliquer sur "Nouvel événement"
   - Remplir les informations (nom, description, date)

2. **Ajouter des participants**
   - Sur la page de l'événement, ajouter les participants avec leur nom et email
   - Chaque participant reçoit automatiquement un token unique

3. **Définir les exclusions** (optionnel)
   - Pour chaque participant, cliquer sur "Gérer exclusions"
   - Sélectionner les personnes à exclure

4. **Générer les assignations**
   - Une fois qu'il y a au moins 3 participants, le bouton "Générer les assignations" apparaît
   - Les assignations sont générées de manière aléatoire en respectant les exclusions
   - Les emails sont envoyés automatiquement à tous les participants

5. **Accès participant**
   - Chaque participant reçoit un email avec un lien unique
   - Le lien contient leur token : `/participant/{token}`
   - Ils peuvent voir à qui ils doivent offrir un cadeau

## 🔒 Sécurité

- Les assignations sont **cryptées** dans la base de données
- L'admin ne peut **jamais** voir les assignations
- Chaque participant a un **token unique** de 64 caractères
- Les assignations ne peuvent être générées **qu'une seule fois** par événement

## 🧪 Tests

Lancer les tests :
```bash
php artisan test
```

## 📁 Structure

```
app/
├── Http/Controllers/
│   ├── AssignmentController.php    # Génération des assignations
│   ├── EventController.php          # Gestion des événements
│   ├── ParticipantController.php    # Gestion des participants/exclusions
│   └── ParticipantAccessController.php  # Accès token-based
├── Models/
│   ├── Assignment.php               # Assignations cryptées
│   ├── Event.php                    # Événements
│   ├── Exclusion.php                # Exclusions
│   └── Participant.php              # Participants avec tokens
├── Notifications/
│   └── SecretSantaAssignmentNotification.php  # Email aux participants
└── Services/
    └── AssignmentService.php        # Algorithme de matching
```

## 🛠️ Technologies

- **Laravel 11** - Framework PHP
- **Blade** - Moteur de templates
- **Alpine.js** - JavaScript réactif
- **Tailwind CSS** - Framework CSS
- **SQLite** - Base de données (par défaut)

## 📝 Notes

- Le minimum de participants requis est **3**
- Les assignations sont générées avec un algorithme de backtracking qui respecte les exclusions
- Si les exclusions rendent le matching impossible, une erreur est affichée
- Les participants peuvent consulter leur assignation autant de fois qu'ils le souhaitent

## 🎄 Joyeux Noël !
