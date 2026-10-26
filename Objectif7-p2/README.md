# Objectif7 — Gestion des soumissions

## Installation
1. Importer `database.sql` dans MySQL/MariaDB pour une nouvelle installation.
2. Si vous avez déjà la base `TDW`, sauvegardez-la puis exécutez une seule fois `database_upgrade.sql` dans phpMyAdmin ou le client MySQL. Ce script conserve les données et adapte les anciens statuts.
3. Vérifier les paramètres MySQL dans `db_connection.php`.
4. Déployer le dossier sur un serveur PHP avec l’extension MySQLi activée. Créer le compte administrateur avec `create_admin.php` selon le processus déjà fourni dans le projet.

Le tableau d’administration est accessible dans `admin/dashboard.php` après connexion. Les utilisateurs se gèrent depuis `admin/users.php`.