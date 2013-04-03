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
 * Erweiterte Klasse für einen JsonRPC Server
 */
class Server extends \Zend\Json\Server\Server
{
	use \DragonJsonServer\ServiceManagerTrait;
    use \DragonJsonServer\EventManagerTrait;
    
    /**
     * Gibt den Klassennamen und Methodennamen zu einer API Methode zurück
     * @param string $method
     * @return array
     */
    public function parseMethod($method)
    {
    	$classname = $this->table->getMethod($method)->getCallback()->getClass();
    	$methodarray = explode('.', $method);
    	$methodname = array_pop($methodarray);
    	return [$classname, $methodname];
    }

    /**
     * Verarbeitet einen JsonRPC Request an den JsonRPC Server
     * @param Request $request
     * @return \DragonJsonServer\Response|null
     */
    public function handle($request = false)
    {
        if (!$request) {
            $request = new \DragonJsonServer\Request();
        } elseif (!$request instanceof \DragonJsonServer\Request) {
            throw new \DragonJsonServer\Exception('invalid requestclass', ['requestclass' => get_class($request)]);
        }
        $this->getEventManager()->trigger(
            (new \DragonJsonServer\Event\Request())
                ->setTarget($this)
                ->setRequest($request)
        );
        $this->setResponse(new \DragonJsonServer\Response());
        $returnResponse = $this->getReturnResponse();
        $this->setReturnResponse();
        $response = parent::handle($request);
        $this->setReturnResponse($returnResponse);
        $this->getEventManager()->trigger(
            (new \DragonJsonServer\Event\Response())
                ->setTarget($this)
                ->setRequest($request)
                ->setResponse($response)
        );
        if ($response->isError()) {
            $error = $response->getError();
            $data = $error->getData();
            if ($data instanceof \DragonJsonServer\Exception) {
                $error->setData($data->getData());
            }
        }
        if ($returnResponse) {
            return $response;
        }
        echo $response;
    }

    /**
     * Verarbeitet einen GET oder POST Request an den JsonRPC Server
     * @param array|null $requests
     * @return \Zend\Json\Server\Smd|array
     */
    public function run($requests = null)
    {
        $this->getEventManager()->trigger(
            (new \DragonJsonServer\Event\Bootstrap())
                ->setTarget($this)
        );
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        if (!isset($requests) && 'GET' == $_SERVER['REQUEST_METHOD']) {
            $servicemap = $this
                ->getServiceMap()
                ->setEnvelope(\Zend\Json\Server\Smd::ENV_JSONRPC_2);
            $this->getEventManager()->trigger(
                (new \DragonJsonServer\Event\Servicemap())
                    ->setTarget($this)
                    ->setServicemap($servicemap)
            );
	        if ($this->getReturnResponse()) {
	            return $servicemap;
	        }
	        echo $servicemap;
        } else {
            if (!isset($requests)) {
                $requests = \Zend\Json\Decoder::decode(file_get_contents('php://input'), \Zend\Json\Json::TYPE_ARRAY);
            }
            $data = [];
        	$returnResponse = $this->getReturnResponse();
            $this->setReturnResponse();
            if (isset($requests['requests']) && is_array($requests['requests'])) {
                $responses = [];
                $params = [];
                foreach ($requests['requests'] as $request) {
                    if (isset($request['params'])) {
                        $request['params'] += $params;
                    }
                    $response = $this->handle(new \DragonJsonServer\Request($request))->toArray();
                    if (isset($response['result']) && is_array($response['result'])) {
                        $params += $response['result'];
                    }
                    $responses[] = $response;
                }
                $data['responses'] = $responses;
            } else {
                $data += $this->handle(new \DragonJsonServer\Request($requests))->toArray();
            }
            if (isset($requests['clientmessages'])) {
            	$clientmessages = $requests['clientmessages'];
            	if (isset($clientmessages['from']) && isset($clientmessages['to'])) {
            		$clientmessages = $this->getServiceManager()->get('Clientmessages')
                        ->collectClientmessages($clientmessages['from'], $clientmessages['to'])
	                    ->getClientmessages();
            		if (count($clientmessages) > 0) {
            			$data['clientmessages'] = $clientmessages;
            		}
            	}
            }
	        $this->setReturnResponse($returnResponse);
	        if ($returnResponse) {
	            return $data;
	        }
            echo \Zend\Json\Encoder::encode($data);
        }
    }
}
