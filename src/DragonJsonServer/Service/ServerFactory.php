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
 * Factory zur Erstellung des Server Services
 */
class ServerFactory implements \Zend\ServiceManager\FactoryInterface
{
    /**
     * Erstellt den Server mit den Einstellungen aus den Konfigurationsdateien
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \DragonJsonServer\Service\Server
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
    	$server = new \DragonJsonServer\Service\Server();
    	$config = $serviceLocator->get('Config');
    	if (!isset($config['apicachefile']) || !\Zend\Server\Cache::get($config['apicachefile'], $server)) {
    		foreach ($config['apiclasses'] as $class => $namespace) {
    			if (is_integer($class)) {
    				$class = $namespace;
    				$namespace = str_replace('\\', '.', $class);
    			}
    			$server->setClass($class, $namespace);
    		}
    		if (isset($config['apicachefile'])) {
    			\Zend\Server\Cache::save($config['apicachefile'], $server);
    		}
    	}
    	return $server;
    }
}
