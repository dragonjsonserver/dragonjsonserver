DragonJsonServer 2.x
====================

PHP Framework für JsonRPC 2 APIs mit Zend Framework 2.

## Installation

1. Zend Skeleton Anwendung einrichten, siehe: http://github.com/zendframework/ZendSkeletonApplication
2. Composer installieren, siehe: http://getcomposer.org/
3. Die Schritte 1-6 der Erweiterungen für das Paket "dragonprojects/dragonjsonserver" ausführen
4. Testen: Aufruf der "/jsonrpc2.php" im Browser sollte die SMD anzeigen

## Erweiterungen
Verfügbare Erweiterungen: http://packagist.org/packages/dragonprojects/

1. "composer.json" erweitern um das Require der Erweiterung
2. In das Anwendungsverzeichnis wechseln und "composer update" (Windows) bzw. "php composer.phar update" (Unix, Mac) ausführen
3. Wenn vorhanden Dateien von "/vendor/dragonprojects/%package%/public" in das eigene "public" Verzeichnis kopieren
4. Wenn vorhanden SQL Datei "/vendor/dragonprojects/%package%/data/database/install.sql" in der Datenbank einspielen
5. Wenn vorhanden Einstellungen in der "/config/autoload/global.php" bzw. "/config/autoload/local.php" aus den Vorlagen "/vendor/dragonprojects/%package%/config/global.php" bzw. "/vendor/dragonprojects/%package%/config/local.php" erweitern
6. Die Erweiterung in der "/config/application.config.php" eintragen
