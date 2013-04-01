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
 * Eventklasse für das Event nachdem der aktuelle Request verarbeitet wurde
 */
class Response extends \Zend\EventManager\Event
{
	use \DragonJsonServer\ServiceManagerTrait { 
		getServiceManager as public; 
	}
	
    /**
     * @var string
     */
    protected $name = 'response';

    /**
     * Setzt das Requestobjekt des aktuellen JsonRPC Request
     * @param \DragonJsonServer\Request $request
     * @return Response
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

    /**
     * Setzt das Responseobjekt des aktuellen JsonRPC Request
     * @param \DragonJsonServer\Response $response
     * @return Response
     */
    public function setResponse(\DragonJsonServer\Response $response)
    {
        $this->setParam('response', $response);
        return $this;
    }

    /**
     * Gibt das Responseobjekt des aktuellen JsonRPC Request zurück
     * @return \DragonJsonServer\Response
     */
    public function getResponse()
    {
        return $this->getParam('response');
    }
}
