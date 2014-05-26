<?php
/*
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\Tests\ResourceProxy\Proxy;

use Msl\Tests\ResourceProxy\ResourceProxyTestCase;
use Msl\ResourceProxy\Proxy\Proxy;

/**
 * AbstractProxy Test: Test for class Msl\ResourceProxy\Proxy\AbstractProxy
 *
 * @category  Proxy
 * @package   Msl\Tests\ResourceProxy\Proxy
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class AbstractProxyTest extends ResourceProxyTestCase
{
    /*********************************
     *  D A T A   P R O V I D E R S  *
     *********************************/
    /**
     * DataProvider for AbstractProxy tests.
     * @return array
     */
    public static function providerMockProxy()
    {
        return array(
            array(
                'MOCK_PROXY',
                array(

                ),
                'wrong_source.name'
            ),
        );
    }

    /***************
     *  T E S T S  *
     ***************/
    /**
     * Tests the Proxy Init process
     *
     * @dataProvider providerMockProxy
     * @test
     */
    public function testInit($proxyName, $config)
    {
        // Getting api mock object
        $proxyMock = $this->getAbstractProxyMock($proxyName, $config);

        // Assert if default mock api name is equal to the used mock api name
        $this->assertEquals($proxyMock->getProxyName(), $this->getProxyName());
    }

    /**
     * Tests the Source Object Creation
     *
     * @dataProvider providerMockProxy
     * @test
     */
    public function testGetSourceObjectByName($proxyName, $config)
    {
        // Getting api mock object
        $proxyMock = $this->getAbstractProxyMock($proxyName, $config);

        // Getting actions from config
        $actions = $config['actions'];
        foreach ($actions as $actionFirstName => $actionSet) {
            foreach ($actionSet as $actionSecondName => $actionConfig) {
                // Creating action name from config
                $actionName = $actionFirstName . AbstractHostApi::API_NAME_LEVEL_SEPARATOR . $actionSecondName;
                // Getting Request object
                $request = $proxyMock->getActionRequestByName($actionName);
                // Getting Request type
                $requestType = $actionConfig['request']['type'];
                $requestClassName = $this->getRequestClassName($requestType);
                //Assert that request is an instance of the found request class name
                $this->assertInstanceOf($requestClassName, $request);
            }
        }
    }

    /**
     * Tests the Source Object Creation
     *
     * @dataProvider providerMockProxy
     * @test
     */
    public function testGetSourceObjectByNameException($proxyName, $config, $wrongSourceName)
    {
        // Getting api mock object
        $proxyMock = $this->getAbstractProxyMock($proxyName, $config);

        // Setting expected exception
        $this->setExpectedException('\Msl\ResourceProxy\Exception\BadProxyConfigurationException');

        // Getting actions from config
        $proxyMock->getActionRequestByName($wrongSourceName);
    }
} 