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
 * Abstract class for wrapped PDO
 */
abstract class WrappedPDO extends PDO
{
    /** @var PDO */
    protected $innerPDO;

    /**
     * @param PDO $innerPDO
     */
    public function __construct(PDO $innerPDO)
    {
        $this->innerPDO = $innerPDO;
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        return $this->innerPDO->beginTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        return $this->innerPDO->commit();
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode()
    {
        return $this->innerPDO->errorCode();
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo()
    {
        return $this->errorInfo();
    }


    /**
     * {@inheritDoc}
     */
    public function exec($sql)
    {
        return $this->innerPDO->exec($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($attr)
    {
        return $this->innerPDO->getAttribute($attr);
    }

    /**
     * {@inheritDoc}
     */
    public function inTransaction()
    {
        return $this->innerPDO->inTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId($name = null)
    {
        return $this->innerPDO->lastInsertId($name);
    }

    /**
     * {@inheritDoc}
     */
    public function prepare($sql, $driver_options = array())
    {
        return $this->innerPDO->prepare($sql, $driver_options);
    }

    /**
     * {@inheritDoc}
     */
    public function query($sql)
    {
        return $this->innerPDO->query($sql);
    }

    /**
     * {@inheritDoc}
     */
    public function quote($expr, $parameter_type = PDO::PARAM_STR)
    {
        return $this->innerPDO->quote($expr, $parameter_type);
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack()
    {
        return $this->innerPDO->rollBack();
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($attr, $value)
    {
        return $this->innerPDO->setAttribute($attr, $value);
    }
}
