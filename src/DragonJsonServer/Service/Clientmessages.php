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
    /**
     * @var array
     */
    protected $clientmessages = [];

    /**
     * Fügt der aktuellen Response eine Clientmessage hinzu
     * @param string $key
     * @param array $data
     */
    public function addClientmessage($key, array $data = [])
    {
        if (!isset($this->clientmessages[$key])) {
            $this->clientmessages[$key] = [];
        }
        $this->clientmessages[$key][] = $data;
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
