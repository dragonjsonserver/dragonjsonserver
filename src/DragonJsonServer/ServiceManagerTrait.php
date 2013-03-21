<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer;

/**
 * Trait für den Zugriff auf den ServiceManager der Anwendung
 */
trait ServiceManagerTrait
{
    /**
     * Gibt den ServiceManager der Anwendung zurück
     * @return \Zend\ServiceManager\ServiceManager
     */
    protected function getServiceManager()
    {
    	global $serviceManager;
    	return $serviceManager;
    }
}
