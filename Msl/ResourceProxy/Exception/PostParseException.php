<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Exception;

/**
 * Exception thrown when an error occurs after having parsed a given set of resources
 *
 * @category  Exception
 * @package   Msl\ResourceProxy\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class PostParseException extends \Exception
{
    /**
     * Error array
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Class Constructor
     *
     * @param array         $errors     the error messages array
     * @param string        $message    the message for this exception
     * @param int           $code       the code for this exception
     * @param \Exception    $previous   the previous exception object
     */
    public function __construct(array $errors = array(), $message = "", $code = 0, \Exception $previous = null)
    {
        // Setting object fields
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Sets the error array
     *
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Returns the error array
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}