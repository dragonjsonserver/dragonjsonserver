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
 * Eventklasse für die Initialisierung der Anwendung
 */
class Bootstrap extends \Zend\EventManager\Event
{
	/**
	 * @var string
	 */
	protected $name = 'Bootstrap';
}
