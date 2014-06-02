/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
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
 * @example
 var method = 'Application.ping';
 var params = {};
 var result = function (result) {};
 var exception = function (exception) {};

 var request = DragonJsonServer.Request(method);
 var request = DragonJsonServer.Request(method, params);
 var request = DragonJsonServer.Request(method, params, result);
 var request = DragonJsonServer.Request(method, params, result, exception);
 var request = DragonJsonServer.Request(method, result);
 var request = DragonJsonServer.Request(method, result, exception);
 */
DragonJsonServer.Request = function (method, params, result, exception)
{
    this.method = method;
    if (typeof params == 'function') {
        this.exception = result;
        this.result = params;
        this.params = {};
    } else {
        this.params = params || {};
        this.result = result;
        this.exception = exception;
    }

    /**
     * Gibt die Daten des Requests zum Senden an den Server zurück
     * @return object
     */
    this.toArray = function () {
        return {
            id : this.id,
            method : this.method,
            params : this.params
        };
    };
};

/**
 * Erstellt einen neuen Client mit den übergebenen Daten
 * @param string serverurl
 * @param object clientoptions
 * @constructor
 * @example
 var clienturl = 'http://2x.dragonjsonserver.de/jsonrpc2.php';
 var client = new DragonJsonServer.Client(clienturl);

 var request = DragonJsonServer.Request('Application.ping');
 client.send(request);

 var requestA = DragonJsonServer.Request('Application.ping');
 var requestB = DragonJsonServer.Request('Application.ping');
 client.send([requestA, requestB]);
 */
DragonJsonServer.Client = function (serverurl, clientoptions)
{
    var serverurl = serverurl;
    var clientoptions = clientoptions || {};
    var id = 0;
    var clientmessage = {
        from : parseInt(new Date().getTime() / 1000),
        callbacks : {},
        keyselection : false
    };
    var defaultparams = {};

    var ajaxoptions = {url : serverurl};
    var stringify;
    if (URI().hostname() == URI(serverurl).hostname()) {
        $.extend(ajaxoptions, {
            type : 'POST',
            dataType : 'json'
        });
        stringify = true;
    } else {
        $.extend(ajaxoptions, {
            type : 'GET',
            dataType : 'jsonp'
        });
        stringify = false;
    }

    /**
     * Setzt eine Callbackmethode für Clientmessages
     * @param string key
     * @param function callback
     * @return Client
     */
    this.setClientmessageCallback = function (key, callback)
    {
        clientmessage.callbacks[key] = callback;
        return this;
    };

    /**
     * Setzt mehrere Callbackmethoden für Clientmessages
     * @param object callbacks
     * @return Client
     */
    this.setClientmessageCallbacks = function (callbacks)
    {
        var self = this;
        $.each(callbacks, function (key, callback) {
            self.setClientmessageCallback(key, callback);
        });
        return this;
    };

    /**
     * Entfernt eine Callbackmethode für Clientmessages
     * @param string key
     * @return Client
     */
    this.removeClientmessageCallback = function (key)
    {
        delete clientmessage.callbacks[key];
        return this;
    };

    /**
     * Aktiviert die Keyauswahl für Clientmessages
     * @return Client
     */
    this.enableClientmessageKeyselection = function ()
    {
        clientmessage.keyselection = true;
        return this;
    };

    /**
     * Deaktiviert die Keyauswahl für Clientmessages
     * @return Client
     */
    this.enableClientmessageKeyselection = function ()
    {
        clientmessage.keyselection = false;
        return this;
    };

    /**
     * Setzt einen Defaultparameter für jeden Request
     * @param string key
     * @param mixed value
     * @return Client
     */
    this.setDefaultparam = function (key, value)
    {
        defaultparams[key] = value;
        return this;
    };

    /**
     * Setzt mehrere Defaultparameter für jeden Request
     * @param object defaultparams
     * @return Client
     */
    this.setDefaultparams = function (defaultparams)
    {
        var self = this;
        $.each(defaultparams, function (key, value) {
            self.setDefaultparam(key, value);
        });
        return this;
    };

    /**
     * Entfernt einen Defaultparameter für jeden Request
     * @param string key
     * @return Client
     */
    this.removeDefaultparam = function (key)
    {
        delete defaultparams[key];
        return this;
    };

    /**
     * Sendet einen Request zum Server mit den übergebenen Daten
     * @param DragonJsonServer.Request|array requests
     * @param object sendoptions
     * @return Client
     */
    this.send = function (requests, sendoptions)
    {
        sendoptions = sendoptions || {};
        var data;
        if ($.isArray(requests)) {
            data = {requests: []};
            $.each(requests, function (index, request) {
                request.id = ++id;
                request.params = $.extend({}, defaultparams, request.params);
                data.requests.push(request.toArray());
            });
        } else {
            requests.id = ++id;
            requests.params = $.extend({}, defaultparams, requests.params);
            data = requests.toArray();
        }
        var to = parseInt(new Date().getTime() / 1000);
        var keys = Object.keys(clientmessage.callbacks);
        if (keys.length) {
            $.extend(data, {clientmessages : {from : clientmessage.from, to : to, keys : keys}});
            if (clientmessage.keyselection) {
                $.extend(data.clientmessages, {keys : keys});
            }
        }
        clientmessage.from = to;
        if (stringify) {
            data = JSON.stringify(data);
        }
        $.ajax($.extend({data: data}, ajaxoptions, clientoptions, sendoptions, {
            success: function (json, statusText, jqXHR) {
                if (undefined != json.clientmessages) {
                    $.each(json.clientmessages, function (key, clientmessages) {
                        $.each(clientmessages, function (i, json) {
                            if (undefined != clientmessage.callbacks[key]) {
                                clientmessage.callbacks[key](json);
                            }
                        });
                    });
                }
                var responses = {};
                if (undefined == json.responses) {
                    responses[json.id] = json;
                } else {
                    $.each(json.responses, function (i, response) {
                        responses[response.id] = response;
                    });
                }
                if (!$.isArray(requests)) {
                    requests = [requests];
                }
                $.each(requests, function (index, request) {
                    if (undefined != responses[request.id]) {
                        var response = responses[request.id];
                        if (undefined != response.error) {
                            if (undefined != request.exception) {
                                request.exception(response.error);
                            }
                            if (undefined != sendoptions.exception) {
                                sendoptions.exception(response.error, request);
                            }
                            if (undefined != clientoptions.exception) {
                                clientoptions.exception(response.error, request);
                            }
                        } else if (undefined != response.result) {
                            request.result(response.result);
                        }
                    }
                });
                if (undefined != sendoptions.success) {
                    sendoptions.success(json, statusText, jqXHR, requests);
                }
                if (undefined != clientoptions.success) {
                    clientoptions.success(json, statusText, jqXHR, requests);
                }
            },
            error: function (jqXHR, statusText, errorThrown) {
                if (undefined != sendoptions.error) {
                    sendoptions.error(jqXHR, statusText, errorThrown, requests);
                }
                if (undefined != clientoptions.error) {
                    clientoptions.error(jqXHR, statusText, errorThrown, requests);
                }
            },
            exception : undefined
        }));
        return this;
    };

    /**
     * Sendet eine Anfrage für die SMD zum Server
     * @param object sendoptions
     * @return Client
     */
    this.smd = function (sendoptions) {
        $.ajax($.extend({}, ajaxoptions, {type : 'GET'}, clientoptions, sendoptions));
        return this;
    }
};
