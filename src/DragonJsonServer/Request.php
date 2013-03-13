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
    Zend\Json\Server\Request\Http as ZendRequest;

/**
 * Requestklasse mit allen Angaben eines JsonRPC Requests
 */
class Request extends ZendRequest
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
        if (isset($options)) {
            $this->setOptions($options);
        } else {
            parent::__construct();
        }
    }
}
