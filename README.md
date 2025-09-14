# 🚀 Projet Todo-List Full Stack

Un projet Full Stack moderne de gestion de tâches développé avec Laravel (Backend) et Vue.js (Frontend), intégrant l'authentification JWT, les notifications en temps réel avec WebSockets, et suivant les bonnes pratiques de développement.

## 📋 Sommaire

- [Architecture](#-architecture)
- [Technologies utilisées](#-technologies-utilisées)
- [Fonctionnalités](#-fonctionnalités)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [API Documentation](#-api-documentation)
- [Principes SOLID](#-principes-solid)
- [Structure du projet](#-structure-du-projet)

## 🏗️ Architecture

Le projet suit une architecture moderne séparant clairement le backend et le frontend :

```
todo-list-project/
├── backend/        # Laravel API
└── frontend/       # Vue.js SPA
```

### Backend (Laravel)

- **API RESTful** avec endpoints protégés par JWT
- **Architecture en couches** avec Services et Repositories
- **Principes SOLID** appliqués dans toute l'application
- **Broadcasting temps réel** avec Laravel Echo + Pusher
- **Base de données** SQLite (facilement configurable pour PostgreSQL/MySQL)

### Frontend (Vue.js)

- **SPA moderne** avec Vue 3 + Composition API
- **Gestion d'état** avec Pinia
- **Routing** avec Vue Router
- **Styling** avec Tailwind CSS
- **Notifications temps réel** avec Laravel Echo + Pusher-js

## 🛠️ Technologies utilisées

### Backend
- **Laravel 12** - Framework PHP moderne
- **tymon/jwt-auth** - Authentification JWT
- **pusher/pusher-php-server** - WebSockets
- **SQLite/PostgreSQL/MySQL** - Base de données

### Frontend
- **Vue 3** - Framework JavaScript progressif
- **TypeScript** - Typage statique
- **Pinia** - Gestion d'état moderne
- **Vue Router** - Routing côté client
- **Axios** - Client HTTP
- **Tailwind CSS** - Framework CSS utility-first
- **Laravel Echo + Pusher-js** - WebSockets côté client

## ✨ Fonctionnalités

### 🔐 Authentification
- [x] Inscription utilisateur avec validation complète
- [x] Connexion avec JWT
- [x] Protection des routes API
- [x] Gestion automatique des tokens

### 📝 Gestion des tâches
- [x] **CRUD complet** : Créer, Lire, Modifier, Supprimer
- [x] **Filtrage** par statut et priorité
- [x] **Statuts** : En attente, En cours, Terminé
- [x] **Priorités** : Faible, Moyenne, Élevée
- [x] **Dates d'échéance** avec validation
- [x] **Isolation utilisateur** : chaque utilisateur voit uniquement ses tâches

### 🔔 Notifications temps réel
- [x] **WebSockets** configurés avec Laravel Echo + Pusher
- [x] **Notifications automatiques** lors de la création de tâches
- [x] **Canaux privés** pour chaque utilisateur

### 🏗️ Architecture
- [x] **Principes SOLID** appliqués
- [x] **Repository Pattern** pour l'abstraction des données
- [x] **Service Layer** pour la logique métier
- [x] **Dependency Injection** avec les interfaces
- [x] **Code bien documenté** avec commentaires explicatifs

## 📦 Installation

### Prérequis
- PHP 8.1+
- Composer
- Node.js 16+
- npm ou yarn

### 1. Cloner le projet
```bash
git clone <url-du-repo>
cd todo-list-project
```

### 2. Installation Backend (Laravel)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
```

### 3. Installation Frontend (Vue.js)
```bash
cd ../frontend
npm install
```

## ⚙️ Configuration

### Backend (.env)
```env
APP_NAME="Todo List API"
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite

# Pusher Configuration
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=your-cluster

# JWT déjà configuré automatiquement
JWT_SECRET=your-jwt-secret
```

### Frontend (.env)
```env
VITE_API_URL=http://localhost:8000
VITE_PUSHER_APP_KEY=your-app-key
VITE_PUSHER_APP_CLUSTER=your-cluster
```

### Configuration Pusher (optionnelle pour le développement)
1. Créer un compte sur [Pusher](https://pusher.com/)
2. Créer une nouvelle app
3. Remplacer les variables Pusher dans les fichiers .env

## 🚀 Utilisation

### Démarrer le backend
```bash
cd backend
php artisan serve
# API disponible sur http://localhost:8000
```

### Démarrer le frontend
```bash
cd frontend
npm run dev
# Interface disponible sur http://localhost:5173
```

### Comptes de test
Créez un compte via l'interface d'inscription ou utilisez l'endpoint API `/api/auth/register`.

## 📚 API Documentation

### Authentification

#### POST /api/auth/register
```json
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "phone_number": "0123456789",
  "address": "123 Rue Example",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### POST /api/auth/login
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### GET /api/auth/me
Récupère les informations de l'utilisateur connecté.
**Headers:** `Authorization: Bearer {token}`

#### POST /api/auth/logout
Déconnecte l'utilisateur.
**Headers:** `Authorization: Bearer {token}`

### Tâches (Protégées par JWT)

#### GET /api/tasks
Récupère toutes les tâches de l'utilisateur.
**Query params:** `?status=pending&priority=high`
**Headers:** `Authorization: Bearer {token}`

#### POST /api/tasks
```json
{
  "title": "Nouvelle tâche",
  "description": "Description optionnelle",
  "status": "pending",
  "priority": "medium",
  "due_date": "2023-12-31"
}
```

#### GET /api/tasks/{id}
Récupère une tâche spécifique.

#### PUT /api/tasks/{id}
Met à jour une tâche.

#### DELETE /api/tasks/{id}
Supprime une tâche.

## 🎯 Principes SOLID

Le backend respecte strictement les principes SOLID :

### Single Responsibility Principle (SRP)
- **Controllers** : gèrent uniquement les requêtes HTTP
- **Services** : contiennent la logique métier
- **Repositories** : gèrent l'accès aux données
- **Models** : représentent les entités de données

### Open/Closed Principle (OCP)
- **Interfaces** permettent l'extension sans modification
- **Service Layer** peut être étendu facilement

### Liskov Substitution Principle (LSP)
- **Implémentations** peuvent être remplacées sans casser le code
- **TaskServiceInterface** peut avoir plusieurs implémentations

### Interface Segregation Principle (ISP)
- **Interfaces spécialisées** plutôt qu'une interface monolithique
- **TaskRepositoryInterface** et **TaskServiceInterface** séparées

### Dependency Inversion Principle (DIP)
- **Dépendance sur des abstractions** (interfaces)
- **Injection de dépendances** via le service container Laravel

### Exemple d'implémentation :

```php
// Interface (Abstraction)
interface TaskRepositoryInterface
{
    public function getTasksByUser(int $userId): Collection;
}

// Implémentation concrète
class TaskRepository implements TaskRepositoryInterface
{
    public function getTasksByUser(int $userId): Collection
    {
        return Task::forUser($userId)->get();
    }
}

// Service utilisant l'abstraction
class TaskService implements TaskServiceInterface
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {}
}
```

## 📁 Structure du projet

### Backend
```
backend/
├── app/
│   ├── Events/                 # Événements pour WebSockets
│   ├── Http/Controllers/Api/   # Contrôleurs API
│   ├── Models/                 # Modèles Eloquent
│   ├── Repositories/           # Couche d'accès aux données
│   │   └── Contracts/          # Interfaces des repositories
│   ├── Services/               # Couche logique métier
│   │   └── Contracts/          # Interfaces des services
│   └── Providers/              # Service providers
├── database/migrations/        # Migrations de base de données
├── routes/
│   ├── api.php                 # Routes API
│   └── channels.php            # Canaux WebSocket
└── config/                     # Configuration Laravel
```

### Frontend
```
frontend/
├── src/
│   ├── components/             # Composants réutilisables
│   ├── stores/                 # Stores Pinia
│   ├── views/                  # Pages/Vues
│   ├── plugins/                # Plugins (Axios, Echo)
│   ├── router/                 # Configuration du routage
│   └── assets/                 # Ressources statiques
├── public/                     # Fichiers publics
└── dist/                       # Build de production
```

## 📈 Améliorations futures possibles

- [ ] Tests unitaires et d'intégration
- [ ] API de statistiques avancées
- [ ] Upload d'images pour les utilisateurs
- [ ] Notifications par email
- [ ] API de recherche de tâches
- [ ] Partage de tâches entre utilisateurs
- [ ] Mode sombre/clair
- [ ] PWA (Progressive Web App)
- [ ] Dockerisation complète

## 🧪 Tests

Pour implémenter les tests (recommandé pour la production) :

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

## 🤝 Contribution

1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

## 🎓 Objectif pédagogique

Ce projet a été conçu dans un objectif d'apprentissage pour maîtriser :

✅ **Architecture Full Stack moderne**  
✅ **Principes SOLID en PHP**  
✅ **Authentification JWT sécurisée**  
✅ **WebSockets et temps réel**  
✅ **Vue.js 3 + Composition API**  
✅ **Pinia pour la gestion d'état**  
✅ **API REST bien structurée**  
✅ **Séparation des responsabilités**  

Le code est abondamment commenté et suit les meilleures pratiques pour servir de référence dans vos futurs projets professionnels.

**Bonne exploration du code ! 🚀**
