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
 * Eventklasse für das Event bevor die Servicemap ausgeliefert wird
 */
class Servicemap extends \Zend\EventManager\Event
{
    /**
     * @var string
     */
    protected $name = 'Servicemap';

    /**
     * Setzt das Servicemapobjekt des aktuellen JsonRPC Servers
     * @param \Zend\Json\Server\Smd $servicemap
     * @return Servicemap
     */
    public function setServicemap(\Zend\Json\Server\Smd $servicemap)
    {
        $this->setParam('servicemap', $servicemap);
        return $this;
    }

    /**
     * Gibt das Servicemapobjekt des aktuellen JsonRPC Servers zurück
     * @return \Zend\Json\Server\Smd
     */
    public function getServicemap()
    {
        return $this->getParam('servicemap');
    }
}
