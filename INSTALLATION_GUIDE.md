# Guide d'Installation Complet - Package Onboarding Raftfg

Ce guide explique comment installer et configurer le package `raftfg/package_onboarding` dans un nouveau projet Laravel.

## 1. Prérequis
- Laravel 11 ou 12
- MySQL / PostgreSQL
- Composer

## 2. Installation du Package

Ajoutez d'abord le dépôt GitHub à votre `composer.json` :

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Raftfg/package_onboarding.git"
    }
],
```

Puis installez le package :
```bash
composer require raftfg/package_onboarding:dev-main
```

## 3. Configuration

Publiez les fichiers de configuration, migrations et vues :
```bash
php artisan vendor:publish --tag=onboarding-config
php artisan vendor:publish --tag=onboarding-migrations
```

Lancez les migrations :
```bash
php artisan migrate
```

## 4. Configuration des Emails

Dans votre fichier `.env`, configurez votre driver mail (ex: Mailtrap ou Log pour le test) :
```env
MAIL_MAILER=log
ONBOARDING_BRAND_NAME="Votre Marque"
ONBOARDING_BRAND_DOMAIN="votre-domaine.com"
```

## 5. Utilisation

### Routes Web incluses :
- `/onboarding` : Page d'accueil/bienvenue.
- `/onboarding/start` : Formulaire de création.
- `/dashboard` : Interface de gestion (nécessite l'authentification Laravel standard).

### Dashboard
Pour accéder au dashboard, assurez-vous que votre projet Laravel possède un système d'authentification (`php artisan make:auth` ou Laravel Breeze). Le dashboard du package s'intégrera automatiquement.

## 6. Personnalisation des Vues
Si vous souhaitez modifier le design, les vues sont publiées dans `resources/views/vendor/onboarding`. Vous pouvez les éditer directement pour les adapter à votre charte graphique.
