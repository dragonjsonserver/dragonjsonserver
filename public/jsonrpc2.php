<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

chdir(dirname(__DIR__));
require 'init_autoloader.php';

use DragonJsonServer\Server,
    Zend\Mvc\Application;

$serviceManager = Application::init(require 'config/application.config.php')
    ->getServiceManager();
Server::init($serviceManager)
    ->run();
