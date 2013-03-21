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
 * Trait für den Zugriff und einmaligen Erstellung eines EventManagers
 */
trait EventManagerTrait
{
    use \DragonJsonServer\ServiceManagerTrait;

	/**
	 * @var \Zend\EventManager\EventManager
	 */
    protected $eventManager;

    /**
     * Gibt den EventManager der Klasse zurück
     * @return \Zend\EventManager\EventManager
     */
    protected function getEventManager()
    {
    	if (!isset($this->eventManager)) {
    		$this->eventManager = $this->getServiceManager()->get('eventManager')
    		    ->setIdentifiers(__CLASS__);
    	}
    	return $this->eventManager;
    }
}
