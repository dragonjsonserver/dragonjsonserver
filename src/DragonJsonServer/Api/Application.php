<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer\Api;

/**
 * API Klasse für die Verbindungsprüfung und den Daten der Anwendung
 */
class Application
{
    use \DragonJsonServer\ServiceManagerTrait;

    /**
     * Methode zur Verbindungsprüfung
     */
    public function ping()
    {}

    /**
     * Methode zur Rückgabe der Daten der Anwendungen
     * @return array
     */
    public function getApplication()
    {
        $application = $this->getServiceManager()->get('Config')['dragonjsonserver']['application'];
        $application['modules'] = $this->getServiceManager()->get('ApplicationConfig')['modules'];
        return $application;
    }
}
