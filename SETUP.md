# Todo List Application - Setup Guide

## 🔧 Prérequis

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 20.x
- **NPM** ou **Yarn**
- **SQLite** (inclus par défaut)

## 🚀 Installation

### 1. Backend Laravel

```bash
cd backend

# Installer les dépendances
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Générer la clé JWT
php artisan jwt:secret

# Créer la base de données SQLite
touch database/database.sqlite

# Exécuter les migrations
php artisan migrate

# Optionnel: ajouter des données de test
php artisan db:seed
```

### 2. Frontend Vue.js

```bash
cd frontend

# Installer les dépendances
npm install

# Copier le fichier d'environnement
cp .env.example .env.local

# Modifier .env.local avec vos configurations
```

### 3. Configuration Pusher (Notifications temps réel)

1. Créer un compte sur [Pusher.com](https://pusher.com)
2. Créer une nouvelle application
3. Récupérer les clés API
4. Mettre à jour les fichiers `.env`:

**Backend (.env):**
```bash
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

**Frontend (.env.local):**
```bash
VITE_PUSHER_APP_KEY=your_app_key
VITE_PUSHER_APP_CLUSTER=mt1
```

## 🏃‍♂️ Démarrage

### Backend
```bash
cd backend
php artisan serve
```
L'API sera disponible sur `http://localhost:8000`

### Frontend
```bash
cd frontend
npm run dev
```
L'application sera disponible sur `http://localhost:3000`

## 📋 Fonctionnalités

### ✅ Authentification JWT
- Inscription avec nom complet, email, téléphone, adresse, image
- Connexion sécurisée
- Token JWT avec expiration
- Middleware de protection

### ✅ Gestion des tâches (CRUD)
- Créer des tâches avec titre, description, priorité, date d'échéance
- Modifier les tâches existantes
- Marquer comme terminées
- Supprimer des tâches
- Filtres par statut et priorité

### ✅ Notifications temps réel
- Notification lors de la création d'une tâche
- Page dédiée aux notifications
- Connexion WebSocket via Pusher
- Notifications navigateur

### ✅ Architecture professionnelle
- **Backend**: Repository Pattern, Service Layer, SOLID principles
- **Frontend**: Pinia store, Vue Router, TypeScript
- **API**: RESTful, JWT protection, proper HTTP status codes

## 🛠️ Tests

### Backend
```bash
cd backend
php artisan test
```

### Frontend
```bash
cd frontend
npm run test
```

## 📂 Structure du projet

```
todo-list-project/
├── backend/          # API Laravel
│   ├── app/
│   │   ├── Models/   # Modèles Eloquent
│   │   ├── Http/Controllers/Api/  # Contrôleurs API
│   │   ├── Services/ # Couche de logique métier
│   │   ├── Repositories/ # Couche d'accès aux données
│   │   └── Events/   # Événements broadcast
│   ├── routes/api.php
│   └── config/
├── frontend/         # Application Vue.js
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/   # Pinia stores
│   │   ├── router/
│   │   └── plugins/  # Configuration Axios/Echo
│   └── package.json
└── README.md
```

## 🔒 Sécurité

- **JWT** pour l'authentification
- **Middleware** de protection des routes
- **CORS** configuré
- **Validation** des données côté backend
- **Hashage** des mots de passe
- **Protection CSRF** (si activée)

## 🌐 API Endpoints

### Authentification
- `POST /api/auth/register` - Inscription
- `POST /api/auth/login` - Connexion
- `POST /api/auth/logout` - Déconnexion
- `GET /api/auth/me` - Profil utilisateur
- `POST /api/auth/refresh` - Rafraîchir le token

### Tâches (Authentification requise)
- `GET /api/tasks` - Liste des tâches
- `POST /api/tasks` - Créer une tâche
- `GET /api/tasks/{id}` - Détail d'une tâche
- `PUT /api/tasks/{id}` - Modifier une tâche
- `DELETE /api/tasks/{id}` - Supprimer une tâche

## 📱 Interface utilisateur

- **Dashboard** - Vue d'ensemble des tâches
- **Gestion des tâches** - CRUD complet
- **Notifications** - Page dédiée aux notifications temps réel
- **Authentification** - Formulaires de connexion/inscription
- **Interface responsive** - Tailwind CSS

## 🐛 Dépannage

### Erreurs communes

1. **Erreur 419 (CSRF)**
   - Vérifier la configuration CORS
   - S'assurer que l'API URL est correcte

2. **WebSocket ne fonctionne pas**
   - Vérifier les clés Pusher
   - Contrôler la configuration du firewall

3. **Erreur JWT**
   - Regénérer la clé JWT: `php artisan jwt:secret`
   - Vérifier l'expiration du token

4. **Base de données**
   - Vérifier que le fichier SQLite existe
   - Réexécuter les migrations: `php artisan migrate:fresh`

## 📖 Technologies utilisées

### Backend
- **Laravel 11** - Framework PHP
- **JWT Auth** - Authentification
- **Pusher** - WebSockets
- **SQLite** - Base de données

### Frontend
- **Vue.js 3** - Framework JavaScript
- **TypeScript** - Typage statique
- **Pinia** - Gestion d'état
- **Vue Router** - Navigation
- **Tailwind CSS** - Styles
- **Axios** - Requêtes HTTP
- **Laravel Echo** - WebSockets client

## 👥 Contribution

1. Fork du projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit des changements (`git commit -m 'Add AmazingFeature'`)
4. Push sur la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request
