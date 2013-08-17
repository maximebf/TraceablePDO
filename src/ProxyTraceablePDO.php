<?php
/*
 * This file is part of the TraceablePDO package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Traceable PDO which implements magic method and forwards
 * property access and method calls to the inner PDO instance
 */
class ProxyTraceablePDO extends TraceablePDO
{
    public function __get($name)
    {
        return $this->innerPDO->$name;
    }

    public function __set($name, $value)
    {
        $this->innerPDO->$name = $value;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->innerPDO, $name), $args);
    }
}