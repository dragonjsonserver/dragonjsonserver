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
 * Requestklasse mit allen Angaben eines JsonRPC Requests
 */
class Request extends \Zend\Json\Server\Request\Http
{
    /**
     * @var string
     */
    protected $version = '2.0';

    /**
     * Übernimmt die übergebenen Parameter oder holt diese aus den Post Daten
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        } else {
            parent::__construct();
        }
    }
}
