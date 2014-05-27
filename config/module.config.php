<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

/**
 * @return array
 */
return [
	'dragonjsonserver' => [
	    'application' => [
	        'name' => 'DragonJsonServer',
	        'version' => '2.x',
	        'copyright' => 'Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)',
	    ],
	    'apicachefile' => null,
	    'apiclasses' => [
	        '\DragonJsonServer\Api\Application' => 'Application',
	    ],
	    'serverurl' => (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . 'jsonrpc2.php',
	],
	'service_manager' => [
		'factories' => [
            '\DragonJsonServer\Service\Client' => '\DragonJsonServer\Service\ClientFactory',
            '\DragonJsonServer\Service\Server' => '\DragonJsonServer\Service\ServerFactory',
		],
		'invokables' => [
            '\DragonJsonServer\Service\Clientmessages' => '\DragonJsonServer\Service\Clientmessages',
		],
	],
];
