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
 * A PDO proxy which traces statements
 */
class TraceablePDO extends WrappedPDO
{
    protected $tracedStatements = array();

    /**
     * {@inheritDoc}
     */
    public function __construct(PDO $innerPDO)
    {
        parent::__construct($innerPDO);
        $this->innerPDO->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('TraceablePDOStatement', array($this)));
    }

    /**
     * {@inheritDoc}
     */
    public function exec($sql)
    {
        return $this->profileCall('exec', $sql, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function query($sql)
    {
        return $this->profileCall('query', $sql, func_get_args());
    }

    /**
     * Profiles a call to a PDO method
     * 
     * @param string $method
     * @param string $sql
     * @param array $args
     * @return mixed The result of the call
     */
    protected function profileCall($method, $sql, array $args)
    {
        $start = microtime(true);
        $ex = null;

        try {
            $result = call_user_func_array(array($this->innerPDO, $method), $args);
        } catch (PDOException $e) {
            $ex = $e;
        }

        $end = microtime(true);
        $memoryUsage = memory_get_usage(true);
        if ($this->innerPDO->getAttribute(PDO::ATTR_ERRMODE) !== PDO::ERRMODE_EXCEPTION && $result === false) {
            $error = $this->innerPDO->errorInfo();
            $ex = new PDOException($error[2], $error[0]);
        }

        $tracedStmt = new TracedPDOStatement($sql, array(), null, 0, $start, $end, $memoryUsage, $ex);
        $this->addTracedStatement($tracedStmt);

        if ($this->innerPDO->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_EXCEPTION && $ex !== null) {
            throw $ex;
        }
        return $result;
    }

    /**
     * Adds an executed TracedPDOStatement
     *
     * @internal
     * @param TracedPDOStatement $stmt
     */
    public function addTracedStatement(TracedPDOStatement $stmt)
    {
        $this->tracedStatements[] = $stmt;
    }

    /**
     * Returns the list of traced statements as an array of TracedPDOStatement objects
     * 
     * @return array
     */
    public function getTracedStatements()
    {
        return $this->tracedStatements;
    }

    /**
     * Returns the last traced statement
     * 
     * @return TracedPDOStatement
     */
    public function getLastTracedStatement()
    {
        if (empty($this->tracedStatements)) {
            return null;
        }
        return $this->tracedStatements[count($this->tracedStatements) - 1];
    }

    /**
     * Returns the list of failed statements
     * 
     * @return array
     */
    public function getFailedTracedStatements()
    {
        return array_filter($this->tracedStatements, function($s) { return !$s->isSuccess(); });
    }

    /**
     * Returns the accumulated execution time of statements
     * 
     * @return int
     */
    public function getAccumulatedStatementsDuration()
    {
        return array_reduce($this->tracedStatements, function($v, $s) { return $v + $s->getDuration(); });
    }

    /**
     * Returns the peak memory usage while performing statements
     * 
     * @return int
     */
    public function getPeakMemoryUsage()
    {
        return array_reduce($this->tracedStatements, function($v, $s) { $m = $s->getMemoryUsage(); return $m > $v ? $m : $v; });
    }
}
