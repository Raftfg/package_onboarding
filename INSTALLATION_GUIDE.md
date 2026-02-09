# Guide d'Installation : Raftfg Onboarding Package

Ce guide vous accompagne dans l'installation et la configuration du package `raftfg/package_onboarding` dans votre projet Laravel.

## Prérequis
- Laravel 10, 11 ou 12
- PHP 8.1 ou supérieur
- Une base de données configurée (MySQL, PostgreSQL, etc.)

---

## Étape 1 : Ajouter le dépôt GitHub
Étant donné que le package est hébergé sur un dépôt privé ou spécifique, ajoutez-le à votre fichier `composer.json` :

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Raftfg/package_onboarding.git"
    }
]
```

## Étape 2 : Installer le package
Exécutez la commande suivante dans votre terminal :

```bash
composer require raftfg/package_onboarding
```

## Étape 3 : Publier les ressources
Publiez le fichier de configuration et les migrations du package :

```bash
php artisan vendor:publish --provider="Raftfg\OnboardingPackage\OnboardingServiceProvider"
```

Cette commande créera :
1. `config/onboarding.php` : Pour personnaliser les préfixes d'URL, les domaines, etc.
2. Des fichiers dans `database/migrations/` : Pour créer les tables nécessaires.

## Étape 4 : Exécuter les migrations
Créez les tables de base de données requises pour le fonctionnement du package :

```bash
php artisan migrate
```

## Étape 5 : Configuration (Optionnel)
Ouvrez le fichier `config/onboarding.php` pour ajuster les paramètres selon vos besoins :
- `api_prefix` : Définit le préfixe des routes API.
- `brand_domain` : Votre domaine de base pour les sous-domaines.
- `rate_limits` : Pour ajuster la sécurité contre les abus.

## Étape 6 : Accès à l'interface
Une fois configuré, vous pouvez accéder à l'interface d'onboarding par défaut via :
`http://votre-projet.local/onboarding/start`

---

## Utilisation de l'API (Authentification)
Pour utiliser les routes API, vous devez inclure l'en-tête suivant :
- **ID** : `X-Master-Key`
- **Valeur** : La clé définie dans votre table `applications` (que vous devez créer initialement).
