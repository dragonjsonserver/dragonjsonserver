<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer;

/**
 * Responseklasse mit allen Angaben eines JsonRPC Response
 */
class Response extends \Zend\Json\Server\Response\Http
{
    /**
     * Gibt den Response als Array zurück
     * @return array
     */
    public function toArray()
    {
        if ($this->isError()) {
            $response = [
                'error' => $this->getError()->toArray(),
                'id' => $this->getId(),
            ];
        } else {
            $response = [
                'result' => $this->getResult(),
                'id' => $this->getId(),
            ];
        }
        if (null !== ($version = $this->getVersion())) {
            $response['jsonrpc'] = $version;
        }
        return $response;
    }
}
