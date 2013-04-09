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
	        'version' => 'v2.0.0',
	        'copyright' => 'Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)',
	    ],
	    'apicachefile' => null,
	    'apiclasses' => [
	        '\DragonJsonServer\Api\Application' => 'Application',
	    ],
	],
	'service_manager' => [
		'factories' => [
            'Server' => '\DragonJsonServer\Service\ServerFactory',
		],
		'invokables' => [
            'Clientmessages' => '\DragonJsonServer\Service\Clientmessages',
		],
	],
];
