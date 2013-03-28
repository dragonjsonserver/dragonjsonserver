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
 * Klasse zur Initialisierung des Moduls
 */
class Module
{
    /**
     * Gibt die Konfiguration des Moduls zurück
     * @return array
     */
    public function getConfig()
    {
        return require __DIR__ . '/config/module.config.php';
    }

    /**
     * Gibt die Autoloaderkonfiguration des Moduls zurück
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * Wird beim Bootstrap des Moduls aufgerufen
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function onBootstrap(\Zend\Mvc\MvcEvent $event)
    {
    	$serviceManager = $event->getApplication()->getServiceManager();
    	$sharedEventManager = $serviceManager->get('sharedEventManager');
    	foreach ($serviceManager->get('Config')['eventlisteners'] as $eventlistener) {
    		call_user_func_array([$sharedEventManager, 'attach'], $eventlistener);
    	}
    }
}
