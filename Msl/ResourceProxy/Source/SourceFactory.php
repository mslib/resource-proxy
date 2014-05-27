<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Source;

use Msl\ResourceProxy\Exception;

/**
 * SourceFactory: creates an instance of \Msl\ResourceProxy\Source\SourceInterface according to a given source type.
 *
 * @category  Source
 * @package   Msl\ResourceProxy\Source
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class SourceFactory
{
    /**
     * Gets a Source instance according to the type.
     *
     * @param string $type       type of the required Source instance (e.g. 'imap')
     * @param string $name       name of the returned Source instance (e.g. 'imap.main.account')
     * @param array  $parameters configuration parameters to initialize a Source instance
     * @param array $globalParameters   Global configuration array: if required parameters are not specified in the parameters array, then we check if they are defined in this array
     *
     * @return \Msl\ResourceProxy\Source\SourceInterface
     *
     * @throws \Msl\ResourceProxy\Exception\BadSourceConfigConfigurationException
     */
    public function getSourceInstance($type, $name, array $parameters, array $globalParameters = array())
    {
        // The source object variable
        $sourceObj = null;

        // Getting SourceConfig object for an Imap Source object
        $sourceConfig = $this->getSourceConfig($name, $type, $parameters, $globalParameters);

        // Creating a Source object with the given source config object
        if ($sourceConfig instanceof SourceConfig ) {
            // Creating a Source object instance according to the given type
            switch ($type) {
                case SourceConfig::SOURCE_TYPE_IMAP:
                    // Initializing an Imap Source object
                    $sourceObj = new Imap();
                    break;
                case SourceConfig::SOURCE_TYPE_POP:
                    // Initializing a Pop Source object
                    $sourceObj = new Pop();
                    break;
                default:
                    throw new Exception\BadSourceConfigConfigurationException(
                        sprintf(
                            "Unrecognized Parser Source type '%s'. Accepted values are: '%s'",
                            $type,
                            SourceConfig::SOURCE_TYPE_IMAP
                        )
                    );
            }

            // Setting the source config to the new source object
            if ($sourceObj instanceof SourceInterface) {
                $sourceObj->setConfig($sourceConfig);
            } else {
                throw new Exception\BadSourceConfigConfigurationException(
                    sprintf(
                        "Unexpected object type: expected 'Msl\ResourceProxy\Source\SourceInterface' for type '%s' but got '%s'.",
                        $type,
                        get_class($sourceObj)
                    )
                );
            }
        } else {
            throw new Exception\BadSourceConfigConfigurationException(
                sprintf(
                    "Unexpected object type: expected 'Msl\ResourceProxy\Source\SourceConfig' for type '%s' but got '%s'.",
                    $type,
                    get_class($sourceConfig)
                )
            );
        }

        // Returning result
        return $sourceObj;
    }

    /**
     * Returns an instance of SourceConfig object to wrap all required configuration for any Source instance.
     *
     * @param string $name              The Source name
     * @param string $type              The Source type (e.g. 'imap')
     * @param array $parameters         Required configuration array to create a Source instance
     * @param array $globalParameters   Global configuration array: if required parameters are not specified in the parameters array, then we check if they are defined in this array
     *
     * @throws \Msl\ResourceProxy\Exception\BadSourceConfigConfigurationException
     *
     * @return SourceConfig
     */
    protected function getSourceConfig($name, $type, array $parameters, array $globalParameters = array())
    {
        // Initializing result object
        $sourceConfig = new SourceConfig();

        // Checking connection parameters
        if (!isset($parameters['connection'])) {
            throw new Exception\BadSourceConfigConfigurationException(
                sprintf("Missing parameters 'connection' for source configuration '$name'", $name)
            );
        } else {
            // Checking host parameter
            if (!isset($parameters['connection']['host'])) {
                // We check the global configuration parameter
                if (!isset($globalParameters['host'])) {
                    throw new Exception\BadSourceConfigConfigurationException(
                        sprintf("Missing global or local parameters 'host' for source configuration '$name.connection'", $name)
                    );
                } else {
                    $host = $globalParameters['host'];
                }
            } else {
                $host = $parameters['connection']['host'];
            }

            // Checking port parameter
            if (!isset($parameters['connection']['port'])) {
                // We check the global configuration parameter
                if (!isset($globalParameters['port'])) {
                    throw new Exception\BadSourceConfigConfigurationException(
                        sprintf("Missing global or local parameters 'port' for source configuration '$name.connection'", $name)
                    );
                } else {
                    $port = $globalParameters['port'];
                }
            } else {
                $port = $parameters['connection']['port'];
            }

            // Checking ssl parameter
            if (!isset($parameters['connection']['ssl'])) {
                // We check the global configuration parameter
                if (!isset($globalParameters['ssl'])) {
                    throw new Exception\BadSourceConfigConfigurationException(
                        sprintf("Missing global or local parameters 'ssl' for source configuration '$name.connection'", $name)
                    );
                } else {
                    $ssl = $globalParameters['ssl'];
                }
            } else {
                $ssl = $parameters['connection']['ssl'];
            }

            // Checking username parameter
            if (!isset($parameters['connection']['username'])) {
                throw new Exception\BadSourceConfigConfigurationException(
                    sprintf("Missing parameters 'username' for source configuration '$name.username'", $name)
                );
            }
            $username = $parameters['connection']['username'];

            // Checking password parameter
            if (!isset($parameters['connection']['password'])) {
                throw new Exception\BadSourceConfigConfigurationException(
                    sprintf("Missing parameters 'password' for source configuration '$name.password'", $name)
                );
            }
            $password = $parameters['connection']['password'];

            // Populating SourceConfig object
            $sourceConfig->setName($name);
            $sourceConfig->setHost($host);
            $sourceConfig->setUsername($username);
            $sourceConfig->setPassword($password);
            $sourceConfig->setCryptProtocol($ssl);
            $sourceConfig->setPort($port);
            $sourceConfig->setType($type);
            if (isset($parameters['connection']['filter'])) {
                $sourceConfig->setFilter($parameters['connection']['filter']);
            }
        }

        // Returning SourceConfig object
        return $sourceConfig;
    }
}
