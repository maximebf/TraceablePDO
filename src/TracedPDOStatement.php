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
 * Holds information about a statement
 */
class TracedPDOStatement
{
    protected $sql;

    protected $rowCount;

    protected $parameters;

    protected $duration;

    protected $memoryUsage;

    protected $exception;

    /**
     * @param string $sql
     * @param array $params
     * @param string $preparedId
     * @param integer $rowCount
     * @param integer $startTime
     * @param integer $endTime
     * @param integer $memoryUsage
     * @param Exception $e
     */
    public function __construct($sql, array $params = array(), $preparedId = null, $rowCount = 0, $startTime = 0, $endTime = 0, $memoryUsage = 0, Exception $e = null)
    {
        $this->sql = $sql;
        $this->rowCount = $rowCount;
        $this->parameters = $params;
        $this->preparedId = $preparedId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->duration = $endTime - $startTime;
        $this->memoryUsage = $memoryUsage;
        $this->exception = $e;
    }

    /**
     * Returns the SQL string used for the query
     * 
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Returns the SQL string with any parameters used embedded
     * 
     * @return string
     */
    public function getSqlWithParams()
    {
        $sql = $this->sql;
        foreach ($this->parameters as $k => $v) {
            $v = sprintf('<%s>', $v);
            if (!is_numeric($k)) {
                $sql = str_replace($k, $v, $sql);
            } else {
                $p = strpos($sql, '?');
                $sql = substr($sql, 0, $p) . $v. substr($sql, $p + 1);
            }
        }
        return $sql;
    }

    /**
     * Returns the number of rows affected/returned
     * 
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * Returns an array of parameters used with the query
     * 
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the prepared statement id
     * 
     * @return string
     */
    public function getPreparedId()
    {
        return $this->preparedId;
    }

    /**
     * Checks if this is a prepared statement
     * 
     * @return boolean
     */
    public function isPrepared()
    {
        return $this->preparedId !== null;
    }

    /**
     * Returns the time in microsecond when the statement execution started
     * 
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Returns the time in microsecond when the statement execution ended
     * 
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Returns the duration in seconds of the execution
     * 
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Returns the peak memory usage during the execution
     * 
     * @return int
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }

    /**
     * Checks if the statement was successful
     * 
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->exception === null;
    }

    /**
     * Returns the exception triggered
     * 
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Returns the exception's code
     * 
     * @return string
     */
    public function getErrorCode()
    {
        return $this->exception !== null ? $this->exception->getCode() : 0;
    }

    /**
     * Returns the exception's message
     * 
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->exception !== null ? $this->exception->getMessage() : '';
    }
}
