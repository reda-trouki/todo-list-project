# 🚀 Projet Todo-List Full Stack

Un projet Full Stack moderne de gestion de tâches collaboratives développé avec Laravel (Backend) et Vue.js (Frontend), intégrant l'authentification JWT, les notifications en temps réel avec WebSockets, et un système complet de permissions utilisateur.

## 📋 Sommaire

- [Architecture](#-architecture)
- [Technologies utilisées](#-technologies-utilisées)
- [Fonctionnalités](#-fonctionnalités)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [API Documentation](#-api-documentation)
- [Système de permissions](#-système-de-permissions)
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
- **Système de permissions granulaires** pour les tâches
- **Broadcasting temps réel** avec Laravel Echo + Pusher
- **Base de données** SQLite (facilement configurable pour PostgreSQL/MySQL)

### Frontend (Vue.js)

- **SPA moderne** avec Vue 3 + Composition API
- **Gestion d'état** avec Pinia
- **Interface utilisateur intuitive** avec permissions contextuelles
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
- **Lucide Vue** - Icônes modernes
- **Laravel Echo + Pusher-js** - WebSockets côté client

## ✨ Fonctionnalités

### 🔐 Authentification
- [x] Inscription utilisateur avec validation complète
- [x] Connexion avec JWT
- [x] Protection des routes API
- [x] Gestion automatique des tokens
- [x] Profil utilisateur complet (nom, email, téléphone, adresse)

### 📝 Gestion des tâches
- [x] **CRUD complet** : Créer, Lire, Modifier, Supprimer
- [x] **Filtrage avancé** par statut, priorité et recherche textuelle
- [x] **Statuts** : En attente, En cours, Terminé
- [x] **Priorités** : Faible, Moyenne, Élevée  
- [x] **Dates d'échéance** avec validation et alertes visuelles
- [x] **Affichage des dates** : création, échéance avec codes couleur

### 👥 Système d'assignation collaboratif
- [x] **Assignation de tâches** à d'autres utilisateurs
- [x] **Visibilité des tâches** : créées ET assignées
- [x] **Gestion des permissions granulaires** par action
- [x] **Interface contextuelle** selon les droits utilisateur
- [x] **Assignation/Désassignation** par le créateur de la tâche

### 🔒 Système de permissions avancé
- [x] **Édition/Suppression** : Uniquement par le créateur de la tâche
- [x] **Changement de statut** : Par l'utilisateur assigné OU le créateur
- [x] **Assignation** : Uniquement par le créateur de la tâche
- [x] **Visualisation contextuelle** des actions disponibles
- [x] **Protection côté API** avec vérifications de permissions

### 🔔 Notifications temps réel
- [x] **WebSockets** configurés avec Laravel Echo + Pusher
- [x] **Notifications automatiques** lors de la création de tâches
- [x] **Canaux privés** pour chaque utilisateur

### 🎨 Interface utilisateur moderne
- [x] **Design responsive** avec Tailwind CSS
- [x] **Menus contextuels** avec actions selon permissions
- [x] **Indicateurs visuels** pour l'assignation et les échéances
- [x] **Filtres en temps réel** sans rechargement de page
- [x] **Modales modernes** pour création/édition

### 🏗️ Architecture technique
- [x] **Principes SOLID** appliqués
- [x] **Repository Pattern** pour l'abstraction des données
- [x] **Service Layer** avec logique métier complexe
- [x] **Dependency Injection** avec les interfaces
- [x] **Validation côté API** et frontend
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
Récupère toutes les tâches de l'utilisateur (créées ET assignées).
**Query params:** `?status=pending&priority=high`
**Headers:** `Authorization: Bearer {token}`

#### POST /api/tasks
```json
{
  "title": "Nouvelle tâche",
  "description": "Description optionnelle",
  "status": "pending",
  "priority": "medium",
  "due_date": "2023-12-31",
  "assigned_to": 2
}
```

#### GET /api/tasks/{id}
Récupère une tâche spécifique (si créée ou assignée à l'utilisateur).

#### PUT /api/tasks/{id}
Met à jour une tâche avec permissions granulaires :
- **Tous les champs** : Créateur uniquement
- **Status uniquement** : Créateur OU utilisateur assigné

```json
{
  "status": "completed"
}
```

#### DELETE /api/tasks/{id}
Supprime une tâche (créateur uniquement).

#### GET /api/users
Récupère la liste des utilisateurs pour l'assignation.
**Headers:** `Authorization: Bearer {token}`

## 🔒 Système de permissions

Le projet implémente un système de permissions granulaires pour une collaboration sécurisée :

### Règles de permissions

#### **Créateur de tâche** (Task Owner)
✅ Voir la tâche  
✅ Modifier tous les champs (titre, description, priorité, échéance)  
✅ Changer le statut  
✅ Assigner/Désassigner la tâche  
✅ Supprimer la tâche  

#### **Utilisateur assigné** (Assigned User)
✅ Voir la tâche  
✅ Changer le statut uniquement  
❌ Modifier les autres champs  
❌ Assigner/Désassigner  
❌ Supprimer la tâche  

#### **Autre utilisateur**
❌ Aucun accès à la tâche

### Implémentation technique

#### Côté Backend (Laravel)
```php
// Vérification d'accès (créateur OU assigné)
private function verifyTaskAccess(Task $task, User $user): void
{
    if ($task->user_id !== $user->id && $task->assigned_to !== $user->id) {
        throw new Exception('Access denied');
    }
}

// Vérification de propriété (créateur uniquement)
private function verifyTaskOwnership(Task $task, User $user): void
{
    if ($task->user_id !== $user->id) {
        throw new Exception('Owner access required');
    }
}

// Vérification pour changement de statut
private function verifyStatusChangePermission(Task $task, User $user): void
{
    if ($task->user_id !== $user->id && $task->assigned_to !== $user->id) {
        throw new Exception('Only assigned user can change status');
    }
}
```

#### Côté Frontend (Vue.js)
```typescript
// Helper de permissions
const isTaskCreator = (task: Task) => {
  const currentUser = getCurrentUser()
  return task.user_id === currentUser.id
}

const isAssignedToMe = (task: Task) => {
  const currentUser = getCurrentUser()
  return task.assigned_to === currentUser.id
}

const canChangeStatus = (task: Task) => {
  return isTaskCreator(task) || isAssignedToMe(task)
}
```

### Interface utilisateur adaptative

L'interface s'adapte automatiquement selon les permissions :

- **Menu contextuel** : Affiche uniquement les actions autorisées
- **Boutons d'édition** : Visibles pour le créateur uniquement
- **Changement de statut** : Disponible pour créateur ET assigné
- **Assignation** : Contrôlée par le créateur uniquement

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
│   │   └── TaskCreated.php     # Notification temps réel création tâche
│   ├── Http/Controllers/Api/   # Contrôleurs API
│   │   ├── AuthController.php  # Authentification JWT
│   │   └── TaskController.php  # CRUD tâches + assignation
│   ├── Models/                 # Modèles Eloquent
│   │   ├── User.php            # Utilisateur avec relations
│   │   └── Task.php            # Tâche avec assignation
│   ├── Repositories/           # Couche d'accès aux données
│   │   ├── TaskRepository.php  # Requêtes complexes (créées + assignées)
│   │   └── Contracts/          # Interfaces des repositories
│   ├── Services/               # Couche logique métier
│   │   ├── TaskService.php     # Logique + permissions granulaires
│   │   └── Contracts/          # Interfaces des services
│   └── Providers/              # Service providers
│       └── ServiceLayerServiceProvider.php  # Injection dépendances
├── database/
│   └── migrations/             # Migrations de base de données
│       ├── create_users_table.php
│       ├── create_tasks_table.php
│       └── add_assigned_to_to_tasks_table.php  # Fonctionnalité assignation
├── routes/
│   ├── api.php                 # Routes API avec protection JWT
│   └── channels.php            # Canaux WebSocket privés
└── config/                     # Configuration Laravel
```

### Frontend
```
frontend/
├── src/
│   ├── components/             # Composants réutilisables
│   │   └── TaskModal.vue       # Modal création/édition avec assignation
│   ├── stores/                 # Stores Pinia
│   │   ├── auth.ts             # Authentification utilisateur
│   │   └── tasks.ts            # Gestion tâches + permissions
│   ├── views/                  # Pages/Vues
│   │   ├── TasksView.vue       # Liste tâches avec permissions contextuelles
│   │   ├── LoginView.vue       # Connexion
│   │   └── RegisterView.vue    # Inscription
│   ├── plugins/                # Plugins
│   │   ├── axios.ts            # Configuration HTTP + intercepteurs
│   │   └── echo.ts             # WebSockets + canaux privés
│   ├── router/                 # Configuration du routage
│   │   └── index.ts            # Routes protégées
│   └── assets/                 # Ressources statiques
├── public/                     # Fichiers publics
└── dist/                       # Build de production
```

### Base de données (SQLite)
```
users
├── id (Primary Key)
├── full_name
├── email (Unique)
├── phone_number
├── address
├── password (Hashed)
└── timestamps

tasks
├── id (Primary Key)
├── title
├── description (Nullable)
├── status (pending/in_progress/completed)
├── priority (low/medium/high)
├── due_date (Nullable)
├── user_id (Foreign Key → users.id)
├── assigned_to (Foreign Key → users.id, Nullable)
└── timestamps
```

## 📈 Améliorations futures possibles

- [ ] Tests unitaires et d'intégration complets
- [ ] API de statistiques avancées (graphiques de productivité)
- [ ] Upload d'images pour les utilisateurs et tâches
- [ ] Notifications par email pour les assignations
- [ ] API de recherche avancée avec filtres complexes
- [ ] Commentaires sur les tâches assignées
- [ ] Historique des modifications de tâches
- [ ] Étiquettes/Tags pour organiser les tâches
- [ ] Projets avec regroupement de tâches
- [ ] Mode sombre/clair
- [ ] PWA (Progressive Web App)
- [ ] Application mobile (React Native/Flutter)
- [ ] Intégration calendrier (Google Calendar, Outlook)
- [ ] Dockerisation complète avec Docker Compose
- [ ] CI/CD avec GitHub Actions
- [ ] Déploiement automatisé (Vercel, Netlify, DigitalOcean)

## 🎯 Fonctionnalités déjà implémentées

### ✅ Système collaboratif complet
- **Assignation de tâches** entre utilisateurs
- **Permissions granulaires** selon le rôle (créateur/assigné)
- **Visibilité adaptative** des actions dans l'interface
- **Protection API** avec vérifications de sécurité

### ✅ Interface utilisateur avancée  
- **Design responsive** moderne avec Tailwind CSS
- **Menus contextuels** avec actions selon permissions
- **Filtrage en temps réel** sans rechargement
- **Indicateurs visuels** pour assignation et échéances
- **Gestion d'état robuste** avec Pinia + TypeScript

### ✅ Architecture professionnelle
- **Principes SOLID** respectés dans tout le code
- **Separation of Concerns** claire (Services/Repositories)
- **Validation complète** côté API et frontend
- **Broadcasting temps réel** avec WebSockets
- **Gestion d'erreurs centralisée**

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

✅ **Architecture Full Stack moderne** avec séparation claire des responsabilités  
✅ **Principes SOLID en PHP** avec Repository Pattern et Service Layer  
✅ **Authentification JWT sécurisée** avec protection des routes  
✅ **Système de permissions granulaires** pour la collaboration  
✅ **WebSockets et temps réel** avec Laravel Echo + Pusher  
✅ **Vue.js 3 + Composition API** avec TypeScript  
✅ **Pinia pour la gestion d'état** moderne et réactive  
✅ **API REST bien structurée** avec validation et gestion d'erreurs  
✅ **Interface utilisateur adaptative** selon les permissions  
✅ **Base de données relationnelle** avec migrations et relations  
✅ **Sécurité applicative** avec vérifications côté API et frontend  

### 🎯 Points techniques avancés couverts

**🔒 Sécurité & Permissions**
- Authentification JWT avec refresh tokens
- Système de permissions granulaires (créateur vs assigné)
- Protection CSRF et validation des entrées
- Vérifications d'accès côté API et frontend

**🏗️ Architecture logicielle**
- Principes SOLID appliqués rigoureusement
- Dependency Injection avec interfaces
- Separation of Concerns (Controllers/Services/Repositories)
- Code modulaire et extensible

**⚡ Performance & UX**
- Requêtes optimisées avec relations Eloquent
- Gestion d'état réactive sans rechargements
- Interface responsive et moderne
- Notifications temps réel via WebSockets

**🔧 Bonnes pratiques de développement**
- Code documenté et commenté
- Validation côté API et frontend
- Gestion centralisée des erreurs
- Structure de projet professionnelle

Le code est abondamment commenté et suit les meilleures pratiques pour servir de référence dans vos futurs projets professionnels, notamment pour la **collaboration en équipe** avec un système de permissions robuste.

**Bonne exploration du code ! 🚀**
