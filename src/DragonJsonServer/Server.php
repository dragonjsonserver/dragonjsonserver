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
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected static $serviceManager;

    /**
     * @var array
     */
    protected static $clientmessages = array();

    /**
     * @var \Zend\EventManager\EventManager
     */
    protected $eventManager;

    /**
     * Initialisiert die erweiterte Klasse für einen JsonRPC Server
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     */
    public static function init(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        self::setServiceManager($serviceManager);
        $server = new static();
        $config = self::getServiceManager()->get('Config');
        if (!isset($config['servicecachefile']) || !\Zend\Server\Cache::get($config['servicecachefile'], $server)) {
            foreach ($config['serviceclasses'] as $class => $namespace) {
                if (is_integer($class)) {
                    $class = $namespace;
                    $namespace = str_replace('\\', '.', $class);
                }
                $server->setClass($class, $namespace);
            }
            if (isset($config['servicecachefile'])) {
                \Zend\Server\Cache::save($config['servicecachefile'], $server);
            }
        }
        $sharedEventManager = self::getServiceManager()->get('sharedEventManager');
        foreach ($config['eventlisteners'] as $eventlistener) {
            call_user_func_array(array($sharedEventManager, 'attach'), $eventlistener);
        }
        $event = new \DragonJsonServer\Event\Bootstrap();
        $event->setTarget($server);
        $server->getEventManager()->trigger($event);
        return $server;
    }

    /**
     * Setzt den ServiceManager der Anwendung
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     */
    protected static function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        self::$serviceManager = $serviceManager;
    }

    /**
     * Gibt den ServiceManager der Anwendung zurück
     * @return \Zend\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        return self::$serviceManager;
    }

    /**
     * Gibt einen neuen EventManager zurück
     * @return \Zend\EventManager\EventManager
     */
    public static function createEventManager($identifier)
    {
        if (is_object($identifier)) {
            $identifier = get_class($identifier);
        }
        $eventManager = new \Zend\EventManager\EventManager($identifier);
        $eventManager->setSharedManager(
            self::getServiceManager()->get('sharedEventManager')
        );
        return $eventManager;
    }

    /**
     * Fügt der aktuellen Response eine Clientmessage hinzu
     * @param string $index
     * @param array $data
     */
    public static function addClientmessage($key, array $data = array())
    {
        if (!isset(self::$clientmessages[$key])) {
            self::$clientmessages[$key] = array();
        }
        self::$clientmessages[$key][] = $data;
    }

    /**
     * Gibt die Clientmessages der aktuellen Response zurück
     * @return array
     */
    protected static function getClientmessages()
    {
        return self::$clientmessages;
    }

    /**
     * Gibt den EventManager für den JsonRPC Server zurück
     * @return \Zend\EventManager\EventManager
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->eventManager = self::createEventManager($this);
        }
        return $this->eventManager;
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
            $data = array();
            $this->setReturnResponse();
            if (isset($requests['requests']) && is_array($requests['requests'])) {
                $responses = array();
                $params = array();
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
	                $data['clientmessages'] = self::getClientmessages();
            	}
            }
            echo \Zend\Json\Encoder::encode($data);
        }
    }
}
