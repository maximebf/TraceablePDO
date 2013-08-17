<?php

class TraceablePDOTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('create table test(field varchar unique)');
        $this->pdo->exec("insert into test values ('foobar')");
    }

    public function testQuery()
    {
        $t = new TraceablePDO($this->pdo);
        $result = $t->query($sql = 'select * from test');
        $this->assertCount(1, $t->getTracedStatements());
        $s = $t->getLastTracedStatement();
        $this->assertEquals($sql, $s->getSql());
        $this->assertTrue($s->isSuccess());
        $this->assertFalse($s->isPrepared());
        $this->assertGreaterThan(0, $s->getDuration());
        //$this->assertEquals(1, $s->getRowCount());
    }

    public function testQueryFailed()
    {
        $t = new TraceablePDO($this->pdo);
        $t->query('select * from foo');
        $this->assertCount(1, $t->getTracedStatements());
        $this->assertCount(1, $t->getFailedTracedStatements());
        $s = $t->getLastTracedStatement();
        $this->assertFalse($s->isSuccess());
        $this->assertEquals("no such table: foo", $s->getErrorMessage());
    }

    public function testExec()
    {
        $t = new TraceablePDO($this->pdo);
        $result = $t->query($sql = "insert into test values ('baz')");
        $this->assertCount(1, $t->getTracedStatements());
        $s = $t->getLastTracedStatement();
        $this->assertEquals($sql, $s->getSql());
        $this->assertTrue($s->isSuccess());
        $this->assertFalse($s->isPrepared());
        $this->assertGreaterThan(0, $s->getDuration());
    }

    public function testExecutePrepared()
    {
        $t = new TraceablePDO($this->pdo);
        $stmt = $t->prepare($sql = "insert into test values(?)");
        $this->assertEmpty($t->getTracedStatements());
        $stmt->execute(array('baz'));
        $this->assertCount(1, $t->getTracedStatements());
        $s = $t->getLastTracedStatement();
        $this->assertTrue($s->isSuccess());
        $this->assertTrue($s->isPrepared());
        $this->assertNotNull($s->getPreparedId());
        $this->assertGreaterThan(0, $s->getDuration());
        $this->assertCount(1, $p = $s->getParameters());
        $this->assertContains('baz', $p);
    }

    public function testExecutePreparedFailed()
    {
        $t = new TraceablePDO($this->pdo);
        $stmt = $t->prepare($sql = "insert into test values(?)");
        $stmt->execute(array('foobar'));
        $this->assertCount(1, $t->getFailedTracedStatements());
        $s = $t->getLastTracedStatement();
        $this->assertFalse($s->isSuccess());
        $this->assertEquals("column field is not unique", $s->getErrorMessage());
    }
}