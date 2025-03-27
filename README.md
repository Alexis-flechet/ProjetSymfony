# Projet Symfony

Ce projet est une API construite avec Symfony qui permet de gérer des événements musicaux et des artistes.  
Les utilisateurs peuvent consulter les événements, s'inscrire/désinscrire et voir les détails des artistes.

---

## 🚀 Installation et Lancement  

### 📦 1. **Prérequis**
Avant de commencer, assurez-vous d'avoir installé :
- [PHP 8.1+](https://www.php.net/)
- [Composer](https://getcomposer.org/download/)

---

### 🛠️ 2. **Installation du Projet**
Clonez le dépôt et installez les dépendances :
  
  git clone https://github.com/Alexis-flechet/ProjetSymfony.git
  cd ProjetSymfony
  composer install

2. Copiez le fichier .env dans un fichier .env.local
3. Supprimé le '#' de la ligne : #DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
4. Mise en Place de la Base de Données
  php bin/console doctrine:database:create
  php bin/console doctrine:migrations:migrate
5. Lancer le Serveur Symfony
   symfony server:start

Le serveur sera accessible sur http://127.0.0.1:8000/


