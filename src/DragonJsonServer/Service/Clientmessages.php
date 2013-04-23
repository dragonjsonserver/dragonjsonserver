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
 * Serviceklasse für die Clientmessages der aktuellen Response
 */
class Clientmessages
{
	use \DragonJsonServer\ServiceManagerTrait;
	use \DragonJsonServer\EventManagerTrait;

    /**
     * @var array
     */
    protected $clientmessages = [];

    /**
     * Fügt der aktuellen Response eine Clientmessage hinzu
     * @param string $key
     * @param array $data
     * @return Clientmessages
     */
    public function addClientmessage($key, array $data = [])
    {
        if (!isset($this->clientmessages[$key])) {
            $this->clientmessages[$key] = [];
        }
        $this->clientmessages[$key][] = $data;
        return $this;
    }

    /**
     * Sammelt die Clientmessages der aktuellen Response
     * @param integer $from
     * @param integer $to
     * @return Clientmessages
     */
    public function collectClientmessages($from, $to)
    {
        $this->getEventManager()->trigger(
            (new \DragonJsonServer\Event\Clientmessages())
                ->setTarget($this)
                ->setFromTimestamp($from)
                ->setToTimestamp($to)
        );
        return $this;
    }

    /**
     * Gibt die Clientmessages der aktuellen Response zurück
     * @return array
     */
    public function getClientmessages()
    {
        return $this->clientmessages;
    }
}
