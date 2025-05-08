# EasyHR

EasyHR est une application web de gestion RH développée avec Symfony. Elle permet la gestion des utilisateurs, des offres d’emploi, des événements, des participations, des formations, des tâches, etc.

## Fonctionnalités principales

- Authentification et inscription des utilisateurs
- Gestion des profils utilisateurs
- Gestion des offres d’emploi
- Gestion des événements et participations
- Gestion des formations
- Gestion des tâches pour les employés
- Interface d’administration et interface publique
- Notifications et envoi d’e-mails

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-repo>
   cd PI_Dev_AppWeb
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Configurer la base de données**
   - Modifier le fichier `.env` pour renseigner vos accès à la base de données :
     ```
     DATABASE_URL="mysql://root:@127.0.0.1:3306/old_base_1"
     ```
   - Créer la base de données si besoin :
     ```bash
     php bin/console doctrine:database:create
     ```
   - Mettre à jour le schéma :
     ```bash
     php bin/console doctrine:schema:update --force
     ```
   - (Optionnel) Charger les fixtures :
     ```bash
     php bin/console doctrine:fixtures:load
     ```

4. **Lancer le serveur de développement**
   ```bash
   symfony server:start
   ```
   ou
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

## Utilisation

- Accéder à l’application via [http://127.0.0.1:8000](http://127.0.0.1:8000)
- Les utilisateurs non connectés voient uniquement les pages publiques (Home, About, Offres Emploi, Contact, Formations)
- Les utilisateurs connectés accèdent à plus de fonctionnalités (Événements, Profil, Tâches, etc.)

## Structure du projet

- `src/Entity` : Entités Doctrine (User, Evenement, Participation, etc.)
- `src/Controller` : Contrôleurs Symfony
- `src/Form` : Formulaires Symfony
- `templates/` : Vues Twig
- `public/` : Fichiers publics (CSS, JS, images)
- `migrations/` : Fichiers de migration de base de données

## Contribution

Les contributions sont les bienvenues !  
Merci de créer une issue ou une pull request pour toute amélioration ou correction.

## Auteurs

- Dev Land

---

**NB :**  
Pense à adapter ce fichier selon les spécificités de ton projet (nom, fonctionnalités, auteurs, etc.).
