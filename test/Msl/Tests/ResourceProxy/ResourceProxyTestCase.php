<?php
/*
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\Tests\ResourceProxy;

/**
 * ResourceProxy Test Case
 *
 * @category  ResourceProxy
 * @package   Msl\Tests\ResourceProxy
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class ResourceProxyTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests config
     *
     * @var array
     */
    protected $config;

    /**
     * Tests proxy name
     *
     * @var string
     */
    protected $proxyName;

    /**
     * Returns a mock object for Msl\ResourceProxy\Proxy\Proxy class
     *
     * @param string $proxyName the proxy name
     * @param array  $config    the config
     *
     * @return mixed
     */
    protected function getAbstractProxyMock($proxyName, array $config)
    {
        // Setting default mock config
        $this->setConfig($config);
        $this->setProxyName($proxyName);

        // Creating the mock object
        $mock = $this->getMockForAbstractClass(
            'Msl\ResourceProxy\Proxy\Proxy',
            array(
                $proxyName,
                $config,
            )
        );

        // Returning mock object
        return $mock;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $proxyName
     */
    public function setProxyName($proxyName)
    {
        $this->proxyName = $proxyName;
    }

    /**
     * @return string
     */
    public function getProxyName()
    {
        return $this->proxyName;
    }
}