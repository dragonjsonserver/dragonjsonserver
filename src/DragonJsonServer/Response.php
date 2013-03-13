<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer;

use Zend\Json\Server\Response\Http as ZendResponse;

/**
 * Responseklasse mit allen Angaben eines JsonRPC Response
 */
class Response extends ZendResponse
{
    /**
     * Gibt den Response als Array zurück
     * @return array
     */
    public function toArray()
    {
        if ($this->isError()) {
            $response = array(
                'error' => $this->getError()->toArray(),
                'id' => $this->getId(),
            );
        } else {
            $response = array(
                'result' => $this->getResult(),
                'id' => $this->getId(),
            );
        }
        if (null !== ($version = $this->getVersion())) {
            $response['jsonrpc'] = $version;
        }
        return $response;
    }
}
