# TP#2 - Web IV : Conception d’une API REST

## Introduction
Ce document détaille la conception d’une API REST pour gérer des comptes utilisateurs. L’objectif est de créer une API, respectant le paradigme REST (GET, POST, PUT, DELETE), avec des réponses en JSON et des codes HTTP appropriés. Les spécifications exigent un système de tokens uniques à usage unique (nonce), générés après chaque requête réussie, sauf pour l’authentification initiale via `/login`.

## Analyse des Fonctionnalités
L’API offre les fonctionnalités suivantes, regroupées en trois catégories :

- **Authentification** :
  - `POST /login` : Authentifie un utilisateur (username/password) et retourne un token initial.
- **Profil** :
  - `GET /profile/{token}` : Récupère les informations du profil.
  - `PUT /profile/{token}` : Modifie les informations du profil (username, firstname, lastname, email).
  - `PUT /profile/{token}/password` : Met à jour le mot de passe.
  - `POST /profile/{token}/credits` : Ajoute des crédits au compte.
  - `POST /profile/{token}/elevate` : Élève le type de compte (NORMAL → PREMIUM).
- **Transactions** :
  - `POST /profile/{token}/transactions` : Enregistre une nouvelle transaction.
  - `GET /profile/{token}/transactions` : Retourne l’historique des transactions.

Chaque requête réussie (sauf `/login`) doit invalider l’ancien token et en générer un nouveau.

## Conception de la Base de Données

### Schéma
La base de données repose sur trois tables :

| Table         | Colonnes                                                                 | Description                                                                 |
|---------------|--------------------------------------------------------------------------|-----------------------------------------------------------------------------|
| Users         | `user_id` (INT, PK, AUTO_INCREMENT), `username` (VARCHAR(50), UNIQUE, NN), `password` (VARCHAR(255), NN), `firstname` (VARCHAR(50), NN), `lastname` (VARCHAR(50), NN), `email` (VARCHAR(100), UNIQUE, NN), `balance` (DECIMAL(10,2), DEFAULT 0), `type` (ENUM('NORMAL', 'PREMIUM'), DEFAULT 'NORMAL') | Informations des utilisateurs, mot de passe haché (ex. BCrypt).             |
| Transactions  | `transaction_id` (INT, PK, AUTO_INCREMENT), `user_id` (INT, FK to Users, NN), `name` (VARCHAR(100), NN), `price` (DECIMAL(10,2), NN), `quantity` (INT, NN), `total` (DECIMAL(10,2), NN), `created_at` (TIMESTAMPTZ, DEFAULT CURRENT_TIMESTAMP) | Historique des transactions, total = `price * quantity`.                    |
| Tokens        | `token_id` (INT, PK, AUTO_INCREMENT), `token_value` (VARCHAR(100), UNIQUE, NN), `user_id` (INT, FK to Users, NN), `created_at` (TIMESTAMPTZ, DEFAULT CURRENT_TIMESTAMP) | Tokens uniques, supprimés après usage.                                      |

### Gestion des Tokens
Les tokens sont des chaînes de caractère uniques, stockés dans `Tokens`. Après utilisation, ils sont supprimés pour garantir leur usage unique.

---

# Justification des Choix
 - ### Suppression des tokens en PHP :
    - Performance : Pas de données inutiles stockées, table Tokens reste légère.
    - Simplicité : Évite les triggers ou procédures, adapté à un TP.
    - Flexibilité : Permet d’ajouter des logs ou des ajustements sans toucher à la base.
 - ### Rejet des triggers :
    - Trop rigide : difficile de modifier la logique ou conserver un historique si besoin.
    - Complexité inutile pour un projet simple.
- Transaction PDO : Garantit l’atomicité (validation, suppression, insertion) pour éviter les incohérences.