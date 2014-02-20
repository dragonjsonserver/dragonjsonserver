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
 * Erstellt einen neuen Client mit den übergebenen Daten
 * @param string serverurl
 * @param object clientoptions
 * @constructor
 */
DragonJsonServer.Client = function (serverurl, clientoptions)
{
    var serverurl = serverurl;
    var clientoptions = clientoptions || {};
    var id = 0;
    var clientmessage = {
        from: parseInt(new Date().getTime() / 1000),
        callbacks: {},
    };
    var defaultparams = {};

    var ajaxoptions = { url: serverurl };
    var stringify;
    if (URI().domain() == URI(serverurl).domain()) {
        $.extend(ajaxoptions, {
            type: 'POST',
            dataType: 'json'
        });
        stringify = true;
    } else {
        $.extend(ajaxoptions, {
            type: 'GET',
            dataType: 'jsonp'
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
        $.extend(data, {clientmessages: {from: clientmessage.from, to: to}});
        clientmessage.from = to;
        if (stringify) {
            data = JSON.stringify(data);
        }
        $.ajax($.extend({data: data}, ajaxoptions, clientoptions, sendoptions, {
            success: function (json, statusText, jqXHR) {
                if (undefined != json.clientmessages) {
                    $.each(json.clientmessages, function (key, clientmessages) {
                        $.each(clientmessages, function (index, json) {
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
                    $.each(json.responses, function (index, response) {
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
                            } else if (undefined != sendoptions.exception) {
                                sendoptions.exception(response.error, request);
                            } else if (undefined != clientoptions.exception) {
                                clientoptions.exception(response.error, request);
                            }
                        } else if (undefined != response.result) {
                            request.result(response.result);
                        }
                    }
                });
                if (undefined != sendoptions.success) {
                    sendoptions.success(json, statusText, jqXHR, requests);
                } else if (undefined != clientoptions.success) {
                    clientoptions.success(json, statusText, jqXHR, requests);
                }
            },
            error: function (jqXHR, statusText, errorThrown)
            {
                if (undefined != sendoptions.error) {
                    sendoptions.error(jqXHR, statusText, errorThrown, requests);
                } else if (undefined != clientoptions.error) {
                    clientoptions.error(jqXHR, statusText, errorThrown, requests);
                }
            },
            exception: undefined,
        }));
        return this;
    };
    
    /**
     * Sendet eine Anfrage für die SMD zum Server
     * @param object sendoptions
     * @return Client
     */
    this.smd = function (sendoptions) {
        $.ajax($.extend({}, ajaxoptions, {type: 'GET'}, clientoptions, sendoptions));
        return this;
    }
};
