<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

chdir(dirname(__DIR__));
require 'init_autoloader.php';
$serviceManager = \Zend\Mvc\Application::init(
    require 'config/application.config.php'
)->getServiceManager();
