# Mon Projet Symfony

Ce projet est une application web développée avec **Symfony 7.2.6** et utilise **Tailwind CSS** pour le style, géré via **Webpack Encore**. Ce README explique comment installer les dépendances et lancer le projet.

## Prérequis

Avant de commencer, assurez-vous d'avoir les outils suivants installés :

- **PHP** >= 8.2
- **Composer** (pour gérer les dépendances PHP)
- **Node.js** >= 16.x et **npm** (pour gérer les dépendances JavaScript et Webpack)
- **MySQL** ou un autre SGBD compatible avec Symfony (optionnel, selon votre configuration)
- Un terminal pour exécuter les commandes

## Installation des dépendances

### 1. Cloner le projet

Clonez le dépôt Git sur votre machine :

```bash
git clone <URL_DU_DEPOT>
cd <NOM_DU_PROJET>
```

### 2. Installer les dépendances PHP

Utilisez Composer pour installer les dépendances PHP nécessaires au projet Symfony :

```bash
composer install
```

### 3. Installer les dépendances JavaScript et Webpack

Le projet utilise **Webpack Encore** pour compiler les assets et **Tailwind CSS** pour le style. Installez les dépendances Node.js avec npm :

```bash
npm install
```

### 4. Configurer l'environnement

Copiez le fichier `.env` pour créer un fichier `.env.local` et configurez vos variables d'environnement (par exemple, la connexion à la base de données) :

```bash
cp .env .env.local
```

Modifiez `.env.local` pour ajouter vos configurations, comme la connexion à la base de données :

```env
DATABASE_URL="mysql://utilisateur:mot_de_passe@127.0.0.1:3306/nom_de_la_base"
```

### 5. Créer la base de données (si applicable)

Exécutez les commandes suivantes pour créer la base et appliquer les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Compiler les assets avec Webpack Encore

Pour compiler les fichiers CSS et JavaScript (y compris Tailwind CSS) :

```bash
npm run dev
```

Pour une compilation en mode production (fichiers minifiés) :

```bash
npm run build
```

Pour surveiller les modifications en temps réel pendant le développement :

```bash
npm run watch
```

## Lancer le serveur Symfony

Pour démarrer le serveur de développement Symfony :

```bash
symfony server:start
```

Le projet sera accessible à l'adresse `http://127.0.0.1:8000` (ou un autre port si spécifié).

## Structure du projet

- `src/` : Contient le code source de l'application Symfony (contrôleurs, entités, etc.).
- `assets/` : Contient les fichiers JavaScript et CSS, y compris la configuration de Tailwind CSS.
- `templates/` : Contient les templates Twig pour le rendu des vues.
- `public/build/` : Contient les assets compilés par Webpack Encore.

## Notes supplémentaires

- Assurez-vous que le fichier `tailwind.config.js` est correctement configuré pour inclure les chemins de vos templates Twig.
- Si vous rencontrez des erreurs liées à Tailwind ou Webpack, vérifiez que toutes les dépendances sont correctement installées avec `npm install`.
- Pour plus d'informations sur Symfony, consultez la [documentation officielle](https://symfony.com/doc/7.2/index.html).
- Pour Tailwind CSS, référez-vous à la [documentation Tailwind](https://tailwindcss.com/docs).
- Pour Webpack Encore, consultez la [documentation Encore](https://symfony.com/doc/7.2/frontend.html).

## Contenu du .env

```bash
# In all environments, the following files are loaded if they exist,
# the latter taking precedence over the former:
#
#  * .env                contains default values for the environment variables needed by the app
#  * .env.local          uncommitted file with local overrides
#  * .env.$APP_ENV       committed environment-specific defaults
#  * .env.$APP_ENV.local uncommitted environment-specific overrides
#
# Real environment variables win over .env files.
#
# DO NOT DEFINE PRODUCTION SECRETS IN THIS FILE NOR IN ANY OTHER COMMITTED FILES.
# https://symfony.com/doc/current/configuration/secrets.html
#
# Run "composer dump-env prod" to compile .env files for production use (requires symfony/flex >=1.2).
# https://symfony.com/doc/current/best_practices.html#use-environment-variables-for-infrastructure-configuration

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
# Choose one of the transports below
# MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
# MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
MAILER_DSN=null://null
###< symfony/mailer ###
```
