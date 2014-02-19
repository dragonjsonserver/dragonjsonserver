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
	    	'name' => '%name%',
	    	'version' => '%version%',
	    	'copyright' => '%copyright%',
	    ],
	    'apicachefile' => '%filepath%',
	    'apiclasses' => [
			'%classname%' => '%namespace%', 
			'%classname%' => '%namespace%', 
	    ],
	    'server' => '%server%',
    ],
];
