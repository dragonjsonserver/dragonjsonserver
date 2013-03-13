/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

if ('undefined' == typeof DragonJsonClient) {
    DragonJsonClient = {};
}

/**
 * Erstellt einen neuen Client mit den übergebenen Daten
 * @param string serverurl
 * @param object clientoptions
 * @constructor
 */
DragonJsonClient.Client = function (serverurl, clientoptions)
{
    var serverurl = serverurl;
    var clientoptions = clientoptions || {};
    var id = 0;
    var clientmessage = {
        from: new Date().getTime(),
        callbacks: {},
    };
    var defaultparams = {};
    
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
    this.setDefaultParam = function (key, value) 
    {
        defaultparams[key] = value;
        return this;
    };
    
    /**
     * Sendet einen Request zum Server mit den übergebenen Daten
     * @param Request|array requests
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
        var to = new Date().getTime();
        $.extend(data, {clientmessages: {from: clientmessage.from, to: to}});
        clientmessage.from = to;
        $.ajax($.extend({
            url: serverurl,
            type: 'POST',
            dataType: 'json',
            data: JSON.stringify(data),
        }, clientoptions, sendoptions, {
            success: function (data, statusText, jqXHR) {
                if (undefined != data.clientmessages) {
                    $.each(data.clientmessages, function (key, clientmessages) {
                        $.each(clientmessages, function (index, data) {
                            if (undefined != clientmessage.callbacks[key]) {
                                clientmessage.callbacks[key](data);
                            }
                        });
                    });
                }
                data.clientmessages = undefined;
                var responses = {};
                if (undefined == data.responses) {
                    responses[data.id] = data;
                } else {
                    $.each(data.responses, function (index, response) {
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
        $.ajax($.extend({
            url : serverurl,
            dataType : 'json',
        }, clientoptions, sendoptions));
        return this;
    }
};
