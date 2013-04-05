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
 * Ausnahmeklasse mit optionalen Key/Value Daten
 */
class Exception extends \Exception
{
	use \DragonJsonServer\ServiceManagerTrait;
    use \DragonJsonServer\EventManagerTrait;
    
    /**
     * @var array
     */
    protected $data;

    /**
     * Nimmt die Parameter einer Ausnahme und die Key/Value Daten entgegen
     * @param string $message
     * @param array $data
     */
    public function __construct($message = '', array $data = [])
    {
        parent::__construct($message);
        $this->setData($data);
        $this->getEventManager()->trigger(
        	(new \DragonJsonServer\Event\Exception())
        		->setTarget($this)
        );
    }

    /**
     * Setzt die Key/Value Daten der Ausnahme
     * @param array $data
     * @return Exception
     */
    protected function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Gibt die Key/Value Daten der Ausnahme zurück
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
