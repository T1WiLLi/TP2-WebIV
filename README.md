# TP#2 - API REST pour Gestion des Comptes Utilisateurs avec Zephyrus

## Description
Ce projet est une API REST développée dans le cadre du cours "420-4U4-SO Web 4 – Développement Backend et Sécurité". L’objectif est de créer une API sans interface graphique, utilisant le framework privé Zephyrus, pour gérer les comptes utilisateurs avec des fonctionnalités d’authentification, de gestion de profil, de crédits, de transactions, et d’élévation de compte. L’API suit le paradigme REST (GET, POST, PUT, DELETE), retourne des réponses en JSON avec des codes HTTP appropriés, et utilise des tokens uniques à usage unique (nonce) pour sécuriser les requêtes.

## Fonctionnalités
- **Authentification** : Connexion via `/login` (POST) avec username/password, retournant un token unique.
- **Profil** : Gestion des informations utilisateur via `/profile/{token}` (GET, PUT), modification du mot de passe via `/profile/{token}/password` (PUT), ajout de crédits via `/profile/{token}/credits` (POST), et élévation de compte via `/profile/{token}/elevate` (POST).
- **Transactions** : Ajout et consultation d’historique via `/profile/{token}/transactions` (POST, GET).
- **Sécurité** : Tokens générés dynamiquement, supprimés après usage, et validation des données (ex. email, mot de passe).