<?php

class CustomPDO extends PDO
{
    public $foo = 'foo';

    public function bar()
    {
        return 'bar';
    }
}

class ProxyTraceablePDOTest extends PHPUnit_Framework_TestCase
{
    public function testProxy()
    {
        $t = new ProxyTraceablePDO(new CustomPDO('sqlite::memory:'));
        $this->assertEquals('foo', $t->foo);
        $this->assertEquals('bar', $t->bar());
    }
}