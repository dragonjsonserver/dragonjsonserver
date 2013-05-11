<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
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
	        'copyright' => 'Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)',
	    ],
	    'apicachefile' => null,
	    'apiclasses' => [
	        '\DragonJsonServer\Api\Application' => 'Application',
	    ],
	    'server' => (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
	],
	'service_manager' => [
		'factories' => [
            'Client' => '\DragonJsonServer\Service\ClientFactory',
            'Server' => '\DragonJsonServer\Service\ServerFactory',
		],
		'invokables' => [
            'Clientmessages' => '\DragonJsonServer\Service\Clientmessages',
		],
	],
];
