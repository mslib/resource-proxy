<?php
/**
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\ResourceProxy\Resource;

use \Zend\Mail\Storage\Message as ZendMessage;
use Msl\ResourceProxy\Exception;

/**
 * Class PopMessage represents a message from a pop account
 *
 * @category  Resource
 * @package   Msl\ResourceProxy\Resource
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class PopMessage extends Email\Message
{
    /**
     * Output sub-folders
     */
    const MESSAGE_SUB_FOLDER = 'pop';

    /**
     * ToString constants
     */
    const MESSAGE_TO_STRING_PREFIX = 'pop_resource_';
}