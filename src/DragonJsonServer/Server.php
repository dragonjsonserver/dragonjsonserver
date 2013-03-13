<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer;

use DragonJsonServer\Exception,
    DragonJsonServer\Request,
    DragonJsonServer\Response,
    Zend\EventManager\EventManager,
    Zend\Json\Decoder,
    Zend\Json\Encoder,
    Zend\Json\Json,
    Zend\Json\Server\Server as ZendServer,
    Zend\Json\Server\Smd,
    Zend\Server\Cache,
    Zend\ServiceManager\ServiceManager;

/**
 * Erweiterte Klasse für einen JsonRPC Server
 */
class Server extends ZendServer
{
    /**
     * @var ServiceManager
     */
    protected static $serviceManager;

    /**
     * @var array
     */
    protected static $clientmessages = array();

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Initialisiert die erweiterte Klasse für einen JsonRPC Server
     * @param ServiceManager $serviceManager
     */
    public static function init(ServiceManager $serviceManager)
    {
        self::setServiceManager($serviceManager);
        $server = new static();
        $config = self::getServiceManager()->get('Config');
        if (!isset($config['servicecachefile']) || !Cache::get($config['servicecachefile'], $server)) {
            foreach ($config['serviceclasses'] as $class => $namespace) {
                if (is_integer($class)) {
                    $class = $namespace;
                    $namespace = str_replace('\\', '.', $class);
                }
                $server->setClass($class, $namespace);
            }
            if (isset($config['servicecachefile'])) {
                Cache::save($config['servicecachefile'], $server);
            }
        }
        $sharedEventManager = self::getServiceManager()->get('sharedEventManager');
        foreach ($config['eventlisteners'] as $eventlistener) {
            call_user_func_array(array($sharedEventManager, 'attach'), $eventlistener);
        }
        $server->getEventManager()->trigger('bootstrap', $server);
        return $server;
    }

    /**
     * Setzt den ServiceManager der Anwendung
     * @param ServiceManager $serviceManager
     */
    protected static function setServiceManager(ServiceManager $serviceManager)
    {
        self::$serviceManager = $serviceManager;
    }

    /**
     * Gibt den ServiceManager der Anwendung zurück
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return self::$serviceManager;
    }

    /**
     * Gibt einen neuen EventManager zurück
     * @return EventManager
     */
    public static function createEventManager($identifier)
    {
        if (is_object($identifier)) {
            $identifier = get_class($identifier);
        }
        $eventManager = new EventManager($identifier);
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
     * @return EventManager
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
            $request = new Request();
        } elseif (!$request instanceof Request) {
            throw new Exception('invalid request', array('request' => $request));
        }
        $this->getEventManager()->trigger('request', $this, array('request' => $request));
        $this->setResponse(new Response());
        $returnResponse = $this->getReturnResponse();
        $this->setReturnResponse();
        $response = parent::handle($request);
        $this->setReturnResponse($returnResponse);
        $this->getEventManager()->trigger('response', $this, array('request' => $request, 'response' => $response));
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
     * @return Smd|array
     */
    public function run($requests = null)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        if (!isset($requests) && 'GET' == $_SERVER['REQUEST_METHOD']) {
            $servicemap = $this
                ->getServiceMap()
                ->setEnvelope(Smd::ENV_JSONRPC_2);
            $this->getEventManager()->trigger('servicemap', $this, array('servicemap' => $servicemap));
            echo $servicemap;
        } else {
            if (!isset($requests)) {
                $requests = Decoder::decode(file_get_contents('php://input'), Json::TYPE_ARRAY);
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
                        ->handle(new Request($request))
                        ->toArray();
                    if (isset($response['result']) && is_array($response['result'])) {
                        $params += $response['result'];
                    }
                    $responses[] = $response;
                }
                $data['responses'] = $responses;
            } else {
                $data += $this
                    ->handle(new Request($requests))
                    ->toArray();
            }
            if (isset($requests['clientmessages']) && isset($requests['clientmessages']['from']) && isset($requests['clientmessages']['to'])) {
                $this->getEventManager()->trigger('clientmessages', $this, array(
                    'from' => $requests['clientmessages']['from'],
                    'to' => $requests['clientmessages']['to'],
                ));
                $data['clientmessages'] = self::getClientmessages();
            }
            echo Encoder::encode($data);
        }
    }
}
