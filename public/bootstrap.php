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

if (is_file('vendor/autoload.php')) {
	$loader = require 'vendor/autoload.php';
}
$zf2path = getenv('ZF2_PATH');
if ($zf2path) {
	if (isset($loader)) {
		$loader->add('Zend', $zf2path);
	} else {
		require $zf2path . '/Zend/Loader/AutoloaderFactory.php';
		Zend\Loader\AutoloaderFactory::factory([
			'Zend\Loader\StandardAutoloader' => ['autoregister_zf' => true],
		]);
	}
}

$serviceManager = \Zend\Mvc\Application::init(
    require 'config/application.config.php'
)->getServiceManager();
