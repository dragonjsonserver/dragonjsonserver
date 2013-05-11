<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer\Service;

/**
 * Factory zur Erstellung des Client Services
 */
class ClientFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     * Erstellt den Client mit den Einstellungen aus den Konfigurationsdateien
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \DragonJsonServer\Service\Client
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
    	$config = $serviceLocator->get('Config')['dragonjsonserver'];
    	$client = new \DragonJsonServer\Service\Client($config['server']);
    	return $client;
    }
}
