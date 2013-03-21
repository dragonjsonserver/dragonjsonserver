<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServer
 */

namespace DragonJsonServer\Api;

/**
 * API Klasse für verschiedene Testmethoden der Client Server Kommunikation
 */
class Test
{
    use \DragonJsonServer\ServiceManagerTrait;

    /**
     * Methode zum Testen eines Requests mit einem Result
     * @param object $result
     * @return array
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonServer.Client(serverurl);
        var request = new DragonJsonServer.Request(
            'Test.testResult',
            {},
            function (result) {
                console.log(result);
            }
        );
        client.send(request);
     */
    public function testResult($result = ['key' => 'value'])
    {
        return $result;
    }

    /**
     * Methode zum Testen eines Requests mit einer Ausnahme
     * @param string $message
     * @param object $data
     * @throws \DragonJsonServer\Exception
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonServer.Client(serverurl);
        var request = new DragonJsonServer.Request(
            'Test.testException',
            {},
            function () {},
            function (exception) {
                console.log(exception);
            }
        );
        client.send(request);
     */
    public function testException($message = 'message', $data = ['key' => 'value'])
    {
        throw new \DragonJsonServer\Exception($message, $data);
    }

    /**
     * Methode zum Testen eines Requests mit einem Fehler
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonServer.Client(serverurl);
        var request = new DragonJsonServer.Request(
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
     * Erster Methode zum Testen eines Multirequest
     * @param string $value
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonServer.Client(serverurl);
        var requestFirst = new DragonJsonServer.Request(
            'Test.testMultirequestFirst',
            {},
            function (result) {
                console.log(result);
            }
        );
        var requestSecond = new DragonJsonServer.Request(
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
        return ['key' => $value];
    }

    /**
     * Zweiter Methode zum Testen eines Multirequest
     */
    public function testMultirequestSecond($key)
    {
        return ['key' => $key];
    }

    /**
     * Methode zum Testen eines Requests mit einer Clientnachricht
     * @param string $key
     * @param object $data
     * @example
        var serverurl = 'serverurl';
        var client = new DragonJsonServer.Client(serverurl);
        var request = new DragonJsonServer.Request('Test.testClientmessage');
        client
            .setClientmessageCallback('key', function (data) {
                console.log(data);
            })
            .send(request);
     */
    public function testClientmessage($key = 'key', $data = ['key' => 'value'])
    {
        $this->getServiceManager()->get('Clientmessages')
            ->addClientmessage($key, $data);
    }
}
