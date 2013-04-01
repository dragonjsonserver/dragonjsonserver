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
 * Eventklasse für das Event bevor der aktuelle Request verarbeitet wird
 */
class Request extends \Zend\EventManager\Event
{
	use \DragonJsonServer\ServiceManagerTrait { 
		getServiceManager as public; 
	}
	
    /**
     * @var string
     */
    protected $name = 'request';

    /**
     * Setzt das Requestobjekt des aktuellen JsonRPC Request
     * @param \DragonJsonServer\Request $request
     * @return Request
     */
    public function setRequest(\DragonJsonServer\Request $request)
    {
        $this->setParam('request', $request);
        return $this;
    }

    /**
     * Gibt das Requestobjekt des aktuellen JsonRPC Request zurück
     * @return \DragonJsonServer\Request
     */
    public function getRequest()
    {
        return $this->getParam('request');
    }
}
