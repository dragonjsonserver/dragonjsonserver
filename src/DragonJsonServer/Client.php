<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer;

/**
 * Erweiterte Klasse für einen JsonRPC Client
 */
class Client extends \Zend\Json\Server\Client
{
    /**
     * Macht einen Request zum Server und gibt das Ergebnis zurück
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws \DragonJsonServer\Exception
     */
    public function call($method, $params = [])
    {
        $response = $this->doRequest($this->createRequest($method, $params));
        if ($response->isError()) {
            $error = $response->getError();
            throw new \DragonJsonServer\Exception($error->getMessage(), $error->getCode(), $error->getData());
        }
        return $response->getResult();
    }
}
