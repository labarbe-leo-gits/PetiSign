# PétiSign
PétiSign est une site de Pétitions en ligne. Il s'agit d'un projet annuel à l'ESGI.

## Mise en place (Setup)
Pour déployer PétiSign sur un VPS, la marche à suivre est relativement simple :
 1. Téléchargez les outils nécessaires sur votre VPS (Maria DB, PHP, Apache)
 2. Déployez les fichiers de ce répertoire sur votre VPS à la racine apache (/var/www/html) en gardant la même structure
 3. Rendez vous sur `http://[votre_ip]/setup.php`
 4. Rentrez le mot de passe suivant : `3hIe1TuYoGUymZ8iePmwjVBniyGgYq8vmH0NM9i3PtAWKEULUS`

Une fois ces étapes effectuées, PétiSign devrait être déployé et prêt à l'emploi avec tout ce qu'il faut pour pouvoir le gérer via le BackOffice !
<br />***N.B : n'oubliez pas de supprimer par la suite le fichier setup.php !***

## Connexion
Une fois la mise en place effectuée, rendez-vous sur `http://[votre_ip]/Sources/login.php` puis connectez-vous en utilisant les informations suivantes : <br/>
- Adresse e-mail : a@a.a
- Mot de passe : root

<br />***N.B : n'oubliez pas de modifier l'adresse e-mail ainsi que le mot de passe via le BackOffice !***

## CronTab
PétiSign utilise CronTab afin d'automatiser des tâches comme la suppression des logs, les rappels de connection, ... <br />
Afin de pouvoir pleinement profiter de la plateforme, voici ce qu'il faut faire sur votre serveur : <br />
1. `apt update && apt upgrade`
2. `apt install crontab`
3. `crontab -e`
4. Rajoutez les lignes suivantes à la fin du fichier :
   ```
   0 0 * * * php -d variables_order=EGPCS -d register_argc_argv=On -r '$_SERVER["REQUEST_METHOD"] = "GET"; $_SERVER["QUERY_STRING"] = "key=[Clé dans fichier env]"; $_GET["key"] = "[Clé dans fichier env]"; require "/var/www/html/Sources/BackOffice/Processus/auto_clear_ban.php";'
   0 0 1 * * php -d variables_order=EGPCS -d register_argc_argv=On -r '$_SERVER["REQUEST_METHOD"] = "GET"; $_SERVER["QUERY_STRING"] = "key=[Clé dans fichier env]"; require "/var/www/html/Sources/BackOffice/Processus/auto_activity_mailing.php";'
   0 0 1 * * php -d variables_order=EGPCS -d register_argc_argv=On -r '$_SERVER["REQUEST_METHOD"] = "GET"; $_SERVER["QUERY_STRING"] = "key=[Clé dans fichier env]"; require "/var/www/html/Sources/BackOffice/Processus/clear_monthly_logs.php";'
   ```

## Android
Sur Android, un APK est disponible. Il s'agit d'un simple Embed WebView du site https://petisign.cloud mais permet d'accèder à l'application de manière simple.

## Contributions

[@LouisDetraux](https://github.com/Louiss1904)\
[@ThomasFauvart](https://github.com/ThomasFdev)
