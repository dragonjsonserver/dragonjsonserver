DragonJsonServer 2.x
=================================

PHP Framework für JsonRPC 2 APIs mit Zend Framework 2.

## Live Demo
http://2x.dragonjsonserver.de/

### Bedienung
1. Namenraum und Methode auswählen
2. Wenn benötigt Argumente eingeben
3. Anfrage senden und Ausgabe anschauen

### Erste Schritte
1. Account.createAccount: Erstellt einen neuen Account und gibt die Session zurück
2. Avatar.createAvatar: Erstellt einen Avatar für die Spielrunde (gameround_id 1 ist vorhanden)
3. Avatarmessage.createAvatarmessage: Erstellt eine Nachricht zu einem anderen Avatar (to_avatar_id 1 ist vorhanden)

## Installation

1. Git installieren, siehe: http://git-scm.com/
2. DragonJsonServerSkeleton klonen per "git clone https://github.com/dragonprojects/dragonjsonserverskeleton.git"
3. Composer installieren, siehe: http://getcomposer.org/
4. Abhängigkeiten installieren per "composer install" (Windows) bzw. "php composer.phar install" (Unix, Mac)
5. Die "/data/database/install.sql" in der Datenbank einspielen
6. Die "/config/autoload/local.php.template" umbenennen in "local.php" und darin die Daten der Datenbank eintragen
7. Die "/public/index.php" im Browser aufrufen

## Aktualisierung

1. Abhängigkeiten aktualisieren per "composer update" (Windows) bzw. "php composer.phar update" (Unix, Mac) 

Für alle Erweiterungen die aktualisiert wurden:

1. Wenn vorhanden Dateien von "/vendor/dragonprojects/%packagename%/public" in das "/public" Verzeichnis kopieren

## Erweiterungen
Verfügbare Erweiterungen: http://packagist.org/packages/dragonprojects/

1. Aktualisierungen vornehmen, siehe unter dem Punkt "Aktualisierung"
2. "/composer.json" erweitern um das Require der Erweiterung
3. Erweiterung installieren per "composer update" (Windows) bzw. "php composer.phar update" (Unix, Mac)
4. Wenn vorhanden Dateien von "/vendor/dragonprojects/%packagename%/public" in das "/public" Verzeichnis kopieren
5. Wenn vorhanden die "/vendor/dragonprojects/%packagename%/data/database/install.sql" in der Datenbank einspielen
6. Wenn vorhanden Einstellungen in der "/config/autoload/global.php" bzw. "/config/autoload/local.php" aus den Vorlagen "/vendor/dragonprojects/%packagename%/config/global.php" bzw. "/vendor/dragonprojects/%packagename%/config/local.php" erweitern
7. Die Erweiterung in der "/config/application.config.php" eintragen
