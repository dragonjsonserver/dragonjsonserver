<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer\Event;

/**
 * Eventklasse für die Initialisierung der Anwendung
 */
class Bootstrap extends \Zend\EventManager\Event
{
	use \DragonJsonServer\ServiceManagerTrait { 
		getServiceManager as public; 
	}
	
	/**
	 * @var string
	 */
	protected $name = 'bootstrap';

    /**
     * Setzt den Service des Servers der initialisiert wurde
     * @param \DragonJsonServer\Service\Server $serviceServer
     * @return Bootstrap
     */
    public function setServiceServer(\DragonJsonServer\Service\Server $serviceServer)
    {
        $this->setParam('serviceServer', $serviceServer);
        return $this;
    }

    /**
     * Gibt den Service des Servers der initialisiert wurde zurück
     * @return \DragonJsonServer\Service\Server
     */
    public function getServiceServer()
    {
        return $this->getParam('serviceServer');
    }
}
