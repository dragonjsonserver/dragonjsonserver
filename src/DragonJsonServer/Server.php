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
 * Erweiterte Klasse für einen JsonRPC Server
 */
class Server extends \Zend\Json\Server\Server
{
    use \DragonJsonServer\EventManagerTrait;

	/**
	 * Initialisiert den Server mit den API Klassen und Event Listenern
	 */
	public function __construct()
	{
		parent::__construct();
		$config = $this->getServiceManager()->get('Config');
        if (!isset($config['apicachefile']) || !\Zend\Server\Cache::get($config['apicachefile'], $this)) {
            foreach ($config['apiclasses'] as $class => $namespace) {
                if (is_integer($class)) {
                    $class = $namespace;
                    $namespace = str_replace('\\', '.', $class);
                }
                $this->setClass($class, $namespace);
            }
            if (isset($config['apicachefile'])) {
                \Zend\Server\Cache::save($config['apicachefile'], $this);
            }
        }
        $sharedEventManager = $this->getServiceManager()->get('sharedEventManager');
        foreach ($config['eventlisteners'] as $eventlistener) {
            call_user_func_array(array($sharedEventManager, 'attach'), $eventlistener);
        }
        $event = new \DragonJsonServer\Event\Bootstrap();
        $event->setTarget($this);
        $this->getEventManager()->trigger($event);
	}

    /**
     * Verarbeitet einen JsonRPC Request an den JsonRPC Server
     * @param Request $request
     * @return Response
     */
    public function handle($request = false)
    {
        if (!$request) {
            $request = new \DragonJsonServer\Request();
        } elseif (!$request instanceof \DragonJsonServer\Request) {
            throw new \DragonJsonServer\Exception('invalid request', array('request' => $request));
        }
        $event = new \DragonJsonServer\Event\Request();
        $event->setTarget($this)
              ->setRequest($request);
        $this->getEventManager()->trigger($event);
        $this->setResponse(new \DragonJsonServer\Response());
        $returnResponse = $this->getReturnResponse();
        $this->setReturnResponse();
        $response = parent::handle($request);
        $this->setReturnResponse($returnResponse);
        $event = new \DragonJsonServer\Event\Response();
        $event->setTarget($this)
              ->setRequest($request)
              ->setResponse($response);
        $this->getEventManager()->trigger($event);
        if ($response->isError()) {
            $error = $response->getError();
            $data = $error->getData();
            if ($data instanceof Exception) {
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
     * @param Request|array|null $requests
     * @return \Zend\Json\Server\Smd|array
     */
    public function run($requests = null)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        if (!isset($requests) && 'GET' == $_SERVER['REQUEST_METHOD']) {
            $servicemap = $this
                ->getServiceMap()
                ->setEnvelope(\Zend\Json\Server\Smd::ENV_JSONRPC_2);
            $event = new \DragonJsonServer\Event\Servicemap();
            $event->setTarget($this)
                  ->setServicemap($servicemap);
            $this->getEventManager()->trigger($event);
            echo $servicemap;
        } else {
            if (!isset($requests)) {
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
                    $response = $this
                        ->handle(new \DragonJsonServer\Request($request))
                        ->toArray();
                    if (isset($response['result']) && is_array($response['result'])) {
                        $params += $response['result'];
                    }
                    $responses[] = $response;
                }
                $data['responses'] = $responses;
            } else {
                $data += $this
                    ->handle(new \DragonJsonServer\Request($requests))
                    ->toArray();
            }
            if (isset($requests['clientmessages'])) {
            	$clientmessages = $requests['clientmessages'];
            	if (isset($clientmessages['from']) && isset($clientmessages['to'])) {
            		$event = new \DragonJsonServer\Event\Clientmessages();
            		$event->setTarget($this)
            		      ->setFrom($clientmessages['from'])
            		      ->setTo($clientmessages['to']);
	                $this->getEventManager()->trigger($event);
	                $data['clientmessages'] = self::getServiceManager()
	                    ->get('Clientmessages')
	                    ->getClientmessages();
            	}
            }
            echo \Zend\Json\Encoder::encode($data);
        }
    }
}
