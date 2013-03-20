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
return array(
    'application' => array(
        'name' => 'DragonJsonServer',
        'version' => 'v2.0.0',
        'copyright' => 'Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)',
    ),
    'apicachefile' => null,
    'apiclasses' => array(
        '\DragonJsonServer\Api\Application' => 'Application',
        '\DragonJsonServer\Api\Test' => 'Test',
    ),
    'eventlisteners' => array(),
);
