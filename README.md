# DevCoffee Tracker
 
API RESTful de suivi de la consommation de caféine des développeurs, avec une interface Vue 3.
 
L'application permet de :
 
- enregistrer les boissons consommées par chaque développeur ;
- bloquer l'enregistrement au-delà de **400 mg de caféine par jour et par développeur** (erreur `429`) ;
- mesurer la sobriété numérique de l'API (temps de réponse, taille de la réponse, estimation CO2) via des en-têtes HTTP personnalisés.
## Stack technique
 
| Partie | Technologies |
|---|---|
| Back-end | PHP 8+, Slim Framework 4, PHP-DI, Monolog, PDO / MySQL |
| Front-end | Vue 3 (Composition API, `<script setup>`), TypeScript, Vue Router, Pinia, Bun |
| Formats | `application/json` et `application/problem+json` (RFC 7807) |
 
## Prérequis
 
- PHP 8.0 ou supérieur
- [Composer](https://getcomposer.org)
- MySQL (ou MariaDB)
- [Bun](https://bun.sh)
## Structure du projet
 
```
Devcofee_project/
├── backend/
│   ├── composer.json
│   ├── schema.sql
│   ├── public/
│   │   └── index.php
│   ├── src/
│   │   ├── Controllers/      DrinkController, ConsumptionController
│   │   ├── Models/           Drink, Consumption
│   │   ├── Services/         CoffeeService
│   │   ├── Middlewares/      CorsMiddleware, EcoMetricsMiddleware
│   │   └── Responses/        ProblemDetailsResponse
│   └── var/
│       └── logs/app.log
├── frontend/                 Application Vue 3
├── .gitignore
└── README.md
```
 
Le back-end suit la norme PSR-4 avec le namespace `App\`.
 
## Récupérer le projet
 
```bash
git clone https://github.com/achillecoves/Devcofee_project.git
cd Devcofee_project
```
 
## Installation du back-end
 
### 1. Installer les dépendances
 
Toutes les commandes du back-end se lancent depuis le dossier `backend/` :
 
```bash
cd backend
```
 
Si le projet contient déjà un `composer.json` :
 
```bash
composer install
```
 
Pour démarrer un projet de zéro :
 
```bash
composer require slim/slim
```
 
Slim a aussi besoin d'une implémentation PSR-7 et des autres bibliothèques du projet :
 
```bash
composer require slim/psr7 php-di/php-di monolog/monolog
```
 
### 2. Mettre à jour les dépendances
 
```bash
composer update
composer dump-autoload
```
 
`composer dump-autoload` régénère l'autoloader. À relancer après l'ajout d'une classe ou d'un namespace.
 
### 3. Créer la base de données
 
Importer le fichier `backend/schema.sql`, qui crée la base `devcoffee`, les tables `drinks` et `consumptions`, et un jeu de données de test :
 
```bash
mysql -u root -p < backend/schema.sql
```
 
Adapter ensuite les identifiants de connexion PDO (hôte, utilisateur, mot de passe) dans la configuration du projet.
 
### 4. Lancer le serveur
 
Depuis le dossier `backend/` :
 
```bash
php -S localhost:5500 -t public
```
 
L'API est disponible sur `http://localhost:5500/api`.
 
## Installation du front-end
 
Le dossier `frontend/` est déjà présent dans le dépôt. Depuis la racine du projet :
 
```bash
cd frontend
bun install
bun run dev
```
 
L'application est disponible sur `http://localhost:5173`.
 
Le back-end doit être lancé en parallèle, et son `CorsMiddleware` doit autoriser l'origine du front.
 
### Création du front depuis zéro (déjà fait)
 
Pour mémoire, le projet a été créé avec :
 
```bash
bun create vue@latest
```
 
Dans l'assistant : nom du projet `frontend`, fonctionnalités **Pinia** et **Router** (plus TypeScript).
 
## Endpoints de l'API
 
### Boissons
 
| Méthode | Route | Description | Succès |
|---|---|---|---|
| `GET` | `/api/drinks` | Liste toutes les boissons | `200` |
| `POST` | `/api/drinks` | Ajoute une boisson (`name`, `caffeineMg`, `volumeMl` obligatoires) | `201` |
 
### Consommations
 
| Méthode | Route | Description | Succès |
|---|---|---|---|
| `GET` | `/api/consumptions` | 20 dernières consommations, de la plus récente à la plus ancienne | `200` |
| `POST` | `/api/consumptions` | Enregistre une consommation | `201` |
| `DELETE` | `/api/consumptions/{id}` | Supprime une consommation | `204` |
 
Exemple de body pour `POST /api/consumptions` :
 
```json
{ "drinkId": 1, "developerName": "Thomas" }
```
 
Exemple d'élément retourné par `GET /api/consumptions` :
 
```json
{
  "id": 10,
  "developerName": "Thomas",
  "drinkName": "Espresso",
  "caffeineMg": 80,
  "consumedAt": "2026-10-16 14:30:00",
  "_links": {
    "delete": { "href": "http://localhost:5500/api/consumptions/10", "method": "DELETE" }
  }
}
```
 
## Règle métier : limite de caféine
 
La limite est de **400 mg par jour et par développeur**. Si l'ajout d'une boisson fait dépasser ce seuil, l'API répond `429 Too Many Requests`. Atteindre exactement 400 mg est accepté.
 
## Gestion des erreurs (RFC 7807)
 
Toutes les erreurs (`400`, `404`, `429`, `500`) sont renvoyées avec l'en-tête `Content-Type: application/problem+json` :
 
```json
{
  "type": "about:blank",
  "title": "Caffeine Limit Exceeded",
  "status": 429,
  "detail": "Seuil de caféine dépassé ! Thomas a déjà consommé 350mg aujourd'hui (limite : 400mg)."
}
```
 
| Code | Cas |
|---|---|
| `400` | Champ obligatoire manquant |
| `404` | Boisson ou consommation introuvable |
| `429` | Limite quotidienne de caféine dépassée |
 
## Éco-conception
 
Le middleware `EcoMetricsMiddleware` ajoute trois en-têtes à chaque réponse :
 
| En-tête | Description |
|---|---|
| `X-Response-Time-Ms` | Durée de traitement côté serveur, en millisecondes |
| `X-Payload-Size-Bytes` | Taille du corps de la réponse, en octets |
| `X-Estimated-CO2-Grams` | Estimation de l'empreinte carbone |
 
Formule utilisée :
 
```
CO2 (g) = (taille du payload en octets × 0.000000011) + (temps d'exécution en ms × 0.000001)
```
 
Ces en-têtes sont exposés via `Access-Control-Expose-Headers` pour être lisibles par le front, qui les affiche dans le badge Éco-Score.
 
## Logs
 
Les erreurs sont enregistrées avec Monolog dans `var/logs/app.log`. Le dossier `var/logs/` doit être accessible en écriture.
 
## Tester l'API
 
Avec Postman ou `curl` :
 
```bash
curl http://localhost:5500/api/consumptions
 
curl -X POST http://localhost:5500/api/consumptions \
  -H "Content-Type: application/json" \
  -d '{"drinkId": 1, "developerName": "Thomas"}'
```
 
