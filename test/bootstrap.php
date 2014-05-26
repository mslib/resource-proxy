<?php
/*
 * This file is part of the ResourceProxy package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PHPUnit bootstrap file
 *
 * PHP version 5
 *
 * @author "Marco Spallanzani" <mslib.code@gmail.com>
 */
// Vendor dir
$vendorDir = realpath(__DIR__.'/../../../../vendor/');

// Getting loader from composer autoload
$loader = require $vendorDir . '/autoload.php';

// Registering msl tests namespace
$baseDir = dirname($vendorDir);
//TODO wrong test path.. it should be not in the vendor
$loader->set('Msl\\Tests', array($vendorDir . '/mslib/resource-proxy/test'));
