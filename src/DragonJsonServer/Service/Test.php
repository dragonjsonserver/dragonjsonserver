<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer\Service;

use DragonJsonServer\Exception,
    DragonJsonServer\Server;

/**
 * Serviceklasse für die Verbindungsprüfung und den Daten der Anwendung
 */
class Test
{
    /**
     * Service zum Testen eines Requests mit einem Result
     * @param array $result
     * @return array
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonClient.Client(serverurl);
        var request = new DragonJsonClient.Request(
            'Test.testResult',
            {},
            function (result) {
                console.log(result);
            }
        );
        client.send(request);
     */
    public function testResult($result = array('key' => 'value'))
    {
        return $result;
    }

    /**
     * Service zum Testen eines Requests mit einer Ausnahme
     * @param string $message
     * @param array $data
     * @throws Exception
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonClient.Client(serverurl);
        var request = new DragonJsonClient.Request(
            'Test.testException',
            {},
            function () {},
            function (exception) {
                console.log(exception);
            }
        );
        client.send(request);
     */
    public function testException($message = 'message', $data = array('key' => 'value'))
    {
        throw new Exception($message, $data);
    }

    /**
     * Service zum Testen eines Requests mit einem Fehler
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonClient.Client(serverurl);
        var request = new DragonJsonClient.Request(
            'Test.testError',
            {}
        );
        client.send(request, {
            error: function (jqXHR, statusText, errorThrown, requests) {
                console.log(jqXHR);
                console.log(statusText);
                console.log(errorThrown);
                if ($.isArray(requests)) {
                    $.each(requests, function (index, request) {
                        console.log(request);
                    });
                } else {
                    console.log(requests);
                }
            }
        });
     */
    public function testError()
    {
        die();
    }

    /**
     * Erster Service zum Testen eines Multirequest
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonClient.Client(serverurl);
        var requestFirst = new DragonJsonClient.Request(
            'Test.testMultirequestFirst',
            {},
            function (result) {
                console.log(result);
            }
        );
        var requestSecond = new DragonJsonClient.Request(
            'Test.testMultirequestSecond',
            {},
            function (result) {
                console.log(result);
            }
        );
        client.send([requestFirst, requestSecond]);
     */
    public function testMultirequestFirst($value = 'value')
    {
        return array('key' => $value);
    }

    /**
     * Zweiter Service zum Testen eines Multirequest
     */
    public function testMultirequestSecond($key)
    {
        return array('key' => $key);
    }

    /**
     * Service zum Testen eines Requests mit einer Clientnachricht
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonClient.Client(serverurl);
        var request = new DragonJsonClient.Request('Test.testClientmessage');
        client
            .setClientmessageCallback('key', function (data) {
                console.log(data);
            })
            .send(request);
     */
    public function testClientmessage($key = 'key', $data = array('key' => 'value'))
    {
        Server::addClientmessage($key, $data);
    }
}
