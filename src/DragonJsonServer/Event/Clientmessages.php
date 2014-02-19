<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
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
    /**
     * @var string
     */
    protected $name = 'Clientmessages';

    /**
     * Setzt den Anfangszeitpunkt für das Laden der Clientmessages
     * @param \DateTime $from
     * @return Clientmessages
     */
    public function setFrom(\DateTime $from)
    {
        $this->setParam('from', $from);
        return $this;
    }

    /**
     * Setzt den Anfangszeitpunkt der Clientmessages als Unix Timestamp
     * @param integer $from
     * @return Clientmessages
     */
    public function setFromTimestamp($from)
    {
        $this->setFrom((new \DateTime())->setTimestamp($from));
        return $this;
    }

    /**
     * Gibt den Anfangszeitpunkt für das Laden der Clientmessages zurück
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->getParam('from');
    }

    /**
     * Setzt den Endzeitpunkt für das Laden der Clientmessages
     * @param \DateTime $to
     * @return Clientmessages
     */
    public function setTo(\DateTime $to)
    {
        $this->setParam('to', $to);
        return $this;
    }

    /**
     * Setzt den Endzeitpunkt der Clientmessages als Unix Timestamp
     * @param integer $to
     * @return Clientmessages
     */
    public function setToTimestamp($to)
    {
        $this->setTo((new \DateTime())->setTimestamp($to));
        return $this;
    }

    /**
     * Gibt den Endzeitpunkt für das Laden der Clientmessages zurück
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->getParam('to');
    }
}
