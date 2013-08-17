# TraceablePDO

[![Build Status](https://travis-ci.org/maximebf/TraceablePDO.png?branch=master)](https://travis-ci.org/maximebf/TraceablePDO)

Wrapper around [PDO](http://php.net/manual/en/book.pdo.php) to provide information
about executed statements.

    $pdo = new TraceablePDO(new PDO($dsn));

    $result = $pdo->query('select * from mytable');

    $tracedStmt = $pdo->getLastTracedStatements();
    printf("The last query took %sms to execute", $tracedStmt->getDuration());

Also provides a `ProxyTraceablePDO` for custom PDO sublasses. Wrap your custom
PDO object inside it and property access and method calls will be forwarded.