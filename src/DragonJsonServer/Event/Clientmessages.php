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
 * Eventklasse für das Laden der Clientmessages bei jedem Request
 */
class Clientmessages extends \Zend\EventManager\Event
{
	use \DragonJsonServer\ServiceManagerTrait { 
		getServiceManager as public; 
	}
	
    /**
     * @var string
     */
    protected $name = 'clientmessages';

    /**
     * Setzt den Service für das Sammeln der Clientmessages
     * @param \DragonJsonServer\Service\Clientmessages $serviceClientmessages
     * @return Clientmessages
     */
    public function setServiceClientmessages(\DragonJsonServer\Service\Clientmessages $serviceClientmessages)
    {
        $this->setParam('serviceClientmessages', $serviceClientmessages);
        return $this;
    }

    /**
     * Gibt den Service für das Sammeln der Clientmessages zurück
     * @return \DragonJsonServer\Service\Clientmessages
     */
    public function getServiceClientmessages()
    {
        return $this->getParam('serviceClientmessages');
    }

    /**
     * Setzt den Anfangszeitpunkt für das Laden der Clientmessages
     * @param integer $from
     * @return Clientmessages
     */
    public function setFrom($from)
    {
        $this->setParam('from', $from);
        return $this;
    }

    /**
     * Gibt den Anfangszeitpunkt für das Laden der Clientmessages zurück
     * @return integer
     */
    public function getFrom()
    {
        return $this->getParam('from');
    }

    /**
     * Setzt den Endzeitpunkt für das Laden der Clientmessages
     * @param integer $to
     * @return Clientmessages
     */
    public function setTo($to)
    {
        $this->setParam('to', $to);
        return $this;
    }

    /**
     * Gibt den Endzeitpunkt für das Laden der Clientmessages zurück
     * @return integer
     */
    public function getTo()
    {
        return $this->getParam('to');
    }
}
