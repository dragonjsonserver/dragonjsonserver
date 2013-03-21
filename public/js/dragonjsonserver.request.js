/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

if ('undefined' == typeof DragonJsonServer) {
	DragonJsonServer = {};
}

/**
 * Erstellt einen neuen Request mit den übergebenen Daten
 * @param string method
 * @param object params
 * @param function result
 * @param function exception
 * @constructor
 */
DragonJsonServer.Request = function (method, params, result, exception)
{
    this.id;
    this.method = method;
    this.params = params || {};
    this.result = result || function () {};
    this.exception = exception;
    
    /**
     * Gibt die Daten des Requests zum Senden an den Server zurück
     * @return object
     */
    this.toArray = function ()
    {
        return {
            id: this.id,
            method: this.method,
            params: this.params,
        };
    };
};
