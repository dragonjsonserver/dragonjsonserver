<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
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
    	$definition = $this->table->getMethod($method);
    	if (!$definition) {
    		throw new \DragonJsonServer\Exception('invalid method', ['method' => $method]); 
    	}
    	$classname = $definition->getCallback()->getClass();
    	$methodarray = explode('.', $method);
    	$methodname = array_pop($methodarray);
    	return [$classname, $methodname];
    }

    /**
     * Verarbeitet einen JsonRPC Request an den JsonRPC Server
     * @param Request $request
     * @return \DragonJsonServer\Response|null
     * @throws \DragonJsonServer\Exception
     */
    public function handle($request = false)
    {
    	$response = new \DragonJsonServer\Response();
        $this->setResponse($response);
        $returnResponse = $this->getReturnResponse();
    	try {
	        if (!$request) {
	            $request = new \DragonJsonServer\Request();
	        } elseif (!$request instanceof \DragonJsonServer\Request) {
	            throw new \DragonJsonServer\Exception('invalid requestclass', ['requestclass' => get_class($request)]);
	        }
	        $response->setId($request->getId());
	        $this->getEventManager()->trigger(
	            (new \DragonJsonServer\Event\Request())
	                ->setTarget($this)
	                ->setRequest($request)
	        );
	        $this->setReturnResponse();
	        parent::handle($request);
	        $this->setReturnResponse($returnResponse);
	        $this->getEventManager()->trigger(
	            (new \DragonJsonServer\Event\Response())
	                ->setTarget($this)
	                ->setRequest($request)
	                ->setResponse($response)
	        );
    	} catch (\Exception $exception) {
    		$this->fault($exception->getMessage(), $exception->getCode(), $exception);
    	}
    	if ($response->isError()) {
    		$error = $response->getError();
    		$data = $error->getData();
    		if ($data instanceof \Exception) {
    			try {
    				$this->getEventManager()->trigger(
    					(new \DragonJsonServer\Event\Exception())
    						->setTarget($this)
    						->setException($data)
    				);
    			} catch (\Exception $exception) {
    				$this->fault($exception->getMessage(), $exception->getCode(), $exception);
		    		$error = $response->getError();
		    		$data = $error->getData();
    			}
    		}
    		if ($data instanceof \DragonJsonServer\Exception) {
    			$error->setData($data->getData());
    		} else {
    			$error->setData([]);
    		}
    	}
        if ($returnResponse) {
            return $response;
        }
        $this->display($response);
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
        $returnResponse = $this->getReturnResponse();
        if (!$returnResponse && !headers_sent()) {
            header('Content-Type: application/json');
        }
        if (isset($_GET['requests']) || isset($_GET['method'])) {
            $requests = $_GET;
        }
        if (null === $requests && 'GET' == $_SERVER['REQUEST_METHOD']) {
            $servicemap = $this
                ->getServiceMap()
                ->setEnvelope(\Zend\Json\Server\Smd::ENV_JSONRPC_2);
            $this->getEventManager()->trigger(
                (new \DragonJsonServer\Event\Servicemap())
                    ->setTarget($this)
                    ->setServicemap($servicemap)
            );
	        if ($returnResponse) {
	            return $servicemap;
	        }
	        $this->display($servicemap);
        } else {
            if (null === $requests) {
                $requests = \Zend\Json\Decoder::decode(file_get_contents('php://input'), \Zend\Json\Json::TYPE_ARRAY);
            }
            $data = [];
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
            		$clientmessages = $this->getServiceManager()->get('\DragonJsonServer\Service\Clientmessages')
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
            $this->display(\Zend\Json\Encoder::encode($data));
        }
    }

    /**
     * Gibt die Ausgabe aus und dekoriert sie bei einem JsonP Request
     * @param string $output
     * @return Server
     */
    public function display($output)
    {
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . '(' . $output . ');';
        } else {
            echo $output;
        }
    }
}
