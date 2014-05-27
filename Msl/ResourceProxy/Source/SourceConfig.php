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

/**
 * Source config object.
 *
 * @category  Source
 * @package   Msl\ResourceProxy\Source
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class SourceConfig
{
    /**
     * Source Type Constants
     */
    const SOURCE_TYPE_IMAP      = 'imap';
    const SOURCE_TYPE_POP       = 'pop';
    const SOURCE_CRYPT_PROTOCOL = 'crypt';

    /**
     * Imap Source Type Constants
     */
    const IMAP_TYPE_TEXT   = 'text';
    const IMAP_TYPE_HTML   = 'html';
    const STATUS_ENABLED   = 'enabled';
    const STATUS_DISABLED  = 'disabled';

    /**
     * The source name
     *
     * @var string $name
     */
    protected $name;

    /**
     * The source
     *
     * @var string $source
     */
    protected $source;

    /**
     * The source host
     *
     * @var string $host
     */
    protected $host;

    /**
     * The source authenticate username
     *
     * @var string $username
     */
    protected $username;

    /**
     * The source authenticate password
     *
     * @var string $password
     */
    protected $password;

    /**
     * Cryptographic protocol name
     *
     * @var string $cryptProtocol
     */
    protected $cryptProtocol;

    /**
     * The source host port
     *
     * @var string $port
     */
    protected $port;

    /**
     * The source type (e.g. 'imap')
     *
     * @var string $type
     */
    protected $type = self::IMAP_TYPE_TEXT;

    /**
     * The source filter
     *
     * @var array $filter
     */
    protected $filter;

    /**
     * The source status
     *
     * @var string $status
     */
    protected $status = self::STATUS_ENABLED;

    /**
     * Returns the source id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the source name
     *
     * @param string $name the name
     *
     * @return SourceConfig
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the source name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the source host
     *
     * @param string $host the source host
     *
     * @return SourceConfig
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Returns the source host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the source username
     *
     * @param string $username the source username
     *
     * @return SourceConfig
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Returns the source username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the source password
     *
     * @param string $password the source password
     *
     * @return SourceConfig
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the source password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the source type
     *
     * @param string $type the source type
     *
     * @return SourceConfig
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns the source type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the source filter
     *
     * @param array $filter the source filter
     *
     * @return SourceConfig
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Returns the source filter
     *
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Sets the source source
     *
     * @param string $source the source
     *
     * @return SourceConfig
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Returns the source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the source status
     *
     * @param string $status the source status
     *
     * @return SourceConfig
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the source status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the source port
     *
     * @param string $port
     *
     * @return SourceConfig
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Returns the source port
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the source cryptographic protocol
     *
     * @param string $cryptProtocol
     *
     * @return SourceConfig
     */
    public function setCryptProtocol($cryptProtocol)
    {
        $this->cryptProtocol = $cryptProtocol;

        return $this;
    }

    /**
     * Returns the source cryptographic protocol
     *
     * @return string
     */
    public function getCryptProtocol()
    {
        return $this->cryptProtocol;
    }
}