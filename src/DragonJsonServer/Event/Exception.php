<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer\Event;

/**
 * Eventklasse für das Event wenn eine Ausnahme für den CLient geworfen wird
 */
class Exception extends \Zend\EventManager\Event
{
    /**
     * @var string
     */
    protected $name = 'exception';

    /**
     * Setzt die Ausnahme die für den CLient geworfen wurde
     * @param \Exception $exception
     * @return Exception
     */
    public function setException(\Exception $exception)
    {
        $this->setParam('exception', $exception);
        return $this;
    }

    /**
     * Gibt die Ausnahme die für den CLient geworfen wurde zurück
     * @return \Exception
     */
    public function getException()
    {
        return $this->getParam('exception');
    }
}
