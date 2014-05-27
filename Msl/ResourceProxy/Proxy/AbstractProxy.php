<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Proxy;

use Msl\ResourceProxy\Source\SourceInterface;
use Msl\ResourceProxy\Source\SourceFactory;
use Msl\ResourceProxy\Exception;
use Msl\ResourceProxy\Resource\ResourceInterface;

/**
 * AbstractProxy class.
 *
 * @category  Proxy
 * @package   Msl\ResourceProxy\Proxy
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
abstract class AbstractProxy
{
    /**
     * String containing the name of this proxy. This value will be used mainly for log purposes
     *
     * @var string
     */
    const PROXY_NAME = "PROXY";

    /**
     * Proxy Name
     *
     * @var string
     */
    protected $proxyName;

    /**
     * Transversable collection of Source instances
     *
     * @var \ArrayIterator
     */
    protected $sourcesIterator;

    /**
     * Collection of Source instances
     *
     * @var array
     */
    protected $sources;

    /**
     * Source object factory
     *
     * @var \Msl\ResourceProxy\Source\SourceFactory
     */
    protected $sourceFactory;

    /**
     * Output folder for all resources
     *
     * @var string
     */
    protected $outputFolder;

    /*****************************
     *   C O N S T R U C T O R   *
     *****************************/
    /**
     * Class constructor
     *
     * @param array $config
     *
     * @throws \Msl\ResourceProxy\Exception\BadProxyConfigurationException
     */
    public function __construct($proxyName = null, array $config = null)
    {
        // Setting proxy name
        if (!empty($proxyName)) {
            $this->proxyName = $proxyName;
        } else {
            $this->proxyName = static::PROXY_NAME;
        }

        // Setting sources configuration
        if (!is_array($config)) {
            $config = $this->getDefaultConfig();
        }

        // Initializing source iterator
        $this->sourcesIterator = new \ArrayIterator();
        $this->sources = array();

        // Initializing SourceFactory
        $this->sourceFactory = new SourceFactory();

        // Getting global connection parameters
        if (!isset($config['global'])) {
            $globalParameters = array();
        } else {
            $globalParameters = $config['global'];
        }

        // Setting proxy configuration
        $this->setProxyConfiguration($globalParameters);

        // Checking if configuration matrix contains a parameters array
        if (!isset($config['sources'])) {
            throw new Exception\BadProxyConfigurationException(
                "Missing parameters array 'sources' for remote source configuration"
            );
        } else {
            // Creating Source objects from the configuration parameters
            $this->setSourceList($config['sources'], $globalParameters);
        }
    }

    /*************************************************
     *   C O N F I G U R A T I O N   M E T H O D S   *
     *************************************************/
    /**
     * Returns an array containing the default configuration
     *
     * @return array
     */
    abstract public function getDefaultConfig();

    /**
     * Sets all required proxy instance configuration
     *
     * @param array $config the global config array
     *
     * @throws \Msl\ResourceProxy\Exception\BadProxyConfigurationException
     */
    public function setProxyConfiguration(array $config)
    {
        // Checking if output folder parameter is defined
        if (!isset($config['output_folder'])) {
            throw new Exception\BadProxyConfigurationException(
                "Missing parameters 'output_folder' for proxy configuration"
            );
        }
        $this->outputFolder = $config['output_folder'];
    }

    /**
     * Adds a Source instance to the list of Source instances to be processed by the Proxy.
     *
     * @param SourceInterface $source the Source instance to be added
     */
    public function addSource(SourceInterface $source)
    {
        $this->sourcesIterator->append($source);
    }

    /**
     * Creates and adds to the Source object list all the configured Source instances
     *
     * @param array $sourceParameters the configuration parameter array
     * @param array $globalParameters the global configuration parameter array
     *
     * @throws \Msl\ResourceProxy\Exception\BadProxyConfigurationException
     *
     * @return void
     *
     */
    protected function setSourceList(array $sourceParameters, array $globalParameters = array())
    {
        // Creating as many Source instances as configured in the configuration file
        foreach ($sourceParameters as $key => $parameters) {
            if (!isset($parameters['type'])) {
                throw new Exception\BadProxyConfigurationException(
                    sprintf("Missing parameters 'type' for remote source configuration '%s'", $key)
                );
            } else {
                // Getting the type from the configuration
                $type = $parameters['type'];

                // Catching exception thrown in the SourceFactory
                try {
                    // Getting Source instance from factory
                    $source = $this->sourceFactory->getSourceInstance($type, $key, $parameters, $globalParameters);

                    if ($source instanceof SourceInterface) {
                        // Adding this Source instance to the list of Source instances to be processed by the Proxy
                        $this->addSource($source);
                        $this->sources[$key] = $source;
                    } else {
                        throw new Exception\BadProxyConfigurationException(
                            sprintf(
                                "Expected instance of 'Msl\ResourceProxy\Source\SourceInterface' but got '%s'",
                                get_class($source)
                            )
                        );
                    }
                } catch (Exception\BadSourceConfigConfigurationException $bscce) {
                    throw new Exception\BadProxyConfigurationException(
                        sprintf(
                            "The following exception has been caught while generating a source object for the source '%s': %s",
                            $key,
                            $bscce->getMessage()
                        )
                    );
                }
            }
        }
    }
    /*********************************************************
     *   P R O C E S S   R E S O U R C E S   M E T H O D S   *
     *********************************************************/
    public function processResources()
    {
//TODO
    }

    /**
     * Processes all resources associated to the given Source object
     *
     * @param SourceInterface $source the source object to be processed
     *
     * @throws \Msl\ResourceProxy\Exception\PostParseUnitException
     */
    public function processResourcesBySource(SourceInterface $source)
    {
        // Getting all resources for the given source and process them (save the content in the configured output folder)
        $resources = $source->getContentIterator();
        $globalSuccess = true;
        foreach ($resources as $resourceKey => $resource) {
            if ($resource instanceof ResourceInterface) {
                // Moving the content of the current resource to the output folder
                $success = $resource->moveToOutputFolder($this->outputFolder);
                if (!$success) {
                    $globalSuccess = false;
                }
                try {
                    // Launching the post parse unit action for the current resource object
                    $source->postParseUnitAction($resourceKey, $success);
                } catch (\Exception $e) {
//TODO save the exception in an array and return/launch a unique exception with all the post parse unit exception
                    throw new Exception\PostParseUnitException(
                        sprintf(
                            "The following exception has been caught after having parsed the source object '%s'. Error is: %s",
                            $source->toString(),
                            $e->getMessage()
                        )
                    );
                }
            }
        }
        try {
            // Running the global post parse action for the current source object
            $source->postParseGlobalAction($globalSuccess);
        } catch (\Exception $e) {
//TODO save the exception in an array and return/launch a unique exception with all the post parse unit exception
            ;
        }
//TODO launch a unique exception with all the post parse unit and post global errors
    }

    public function processResourcesBySourceName($sourceName)
    {
//TODO
    }

    /*****************************************************
     *   P A R S E   R E S O U R C E S   M E T H O D S   *
     *****************************************************/
    /**
     * Returns an Iterator instance containing resource data for the current source object (from the iterator).
     *
     * @throws \Msl\ResourceProxy\Exception\SourceGetDataException
     *
     * @return null|Iterator
     */
    public function getCurrentSourceData()
    {
        // Getting the current Source element
        $source = $this->getCurrentSource();

        // Getting the data
        if ($source instanceof SourceInterface) {
            // Getting result iterator
            try {
                return $source->getContentIterator();
            } catch (\Exception $e) {
                throw new Exception\SourceGetDataException(
                    sprintf(
                        'Exception caught while getting the content for the following source object: %s',
                        $source->toString()
                    )
                );
            }
        }
        return null;
    }

    /**
     * Returns the current instance of Source object stored in the 'sources' iterator
     *
     * @return null|SourceInterface
     */
    public function getCurrentSource()
    {
        if ($this->sourcesIterator->valid()) {
            return $this->sourcesIterator->current();
        }
        return null;
    }

    /**
     * Returns true if there are more Source instances to be parsed; false otherwise
     *
     * @return bool
     */
    public function hasMoreSources()
    {
        return $this->sourcesIterator->valid();
    }

    /**
     * Moves the Source iterator pointer to the next Source object
     *
     * @return void
     */
    public function moveToNextSource()
    {
        // Moving the iterator to the next entry
        $this->sourcesIterator->next();
    }

    /**
     * Returns the Source object for the given source name
     *
     * @param string $sourceName the source name
     *
     * @return null|SourceInterface
     */
    public function getSourceByName($sourceName)
    {
        if (isset($this->sources[$sourceName])) {
            return $this->sources[$sourceName];
        }
        return null;
    }

    /**
     * Returns an Iterator instance containing resource data for the given source object
     *
     * @param \Msl\ResourceProxy\Source\SourceInterface $source the source object
     *
     * @throws \Msl\ResourceProxy\Exception\SourceGetDataException
     *
     * @return null|Iterator
     */
    public function getSourceData(SourceInterface $source)
    {
        // Getting result iterator
        try {
            return $source->getContentIterator();
        } catch (\Exception $e) {
            throw new Exception\SourceGetDataException(
                sprintf(
                    'Exception caught while getting the content for the following source object: %s',
                    $source->toString()
                )
            );
        }
    }

    /**
     * Returns an Iterator instance containing resource data for the given source name
     *
     * @param string $sourceName the source name
     *
     * @throws \Msl\ResourceProxy\Exception\SourceGetDataException
     *
     * @return null|Iterator
     */
    public function getSourceDataBySourceName($sourceName)
    {
        // Getting source object first
        $source = $this->getSourceByName($sourceName);

        // Getting the data
        if ($source instanceof SourceInterface) {
            // Getting result iterator
            try {
                return $source->getContentIterator();
            } catch (\Exception $e) {
                throw new Exception\SourceGetDataException(
                    sprintf(
                        'Exception caught while getting the content for the following source object: %s',
                        $source->toString()
                    )
                );
            }
        }
        return null;
    }

    /*************************************
     *   G E N E R A L   M E T H O D S   *
     *************************************/
    /**
     * Returns the name of the current proxy instance (for logs and exceptions)
     *
     * @return string
     */
    public function getProxyName()
    {
        return $this->proxyName;
    }

    /**
     * Returns the resource output folder
     *
     * @return string
     */
    public function getOutputFolder()
    {
        return $this->outputFolder;
    }
}
