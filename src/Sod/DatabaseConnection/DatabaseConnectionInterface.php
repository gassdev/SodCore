<?php

declare(strict_types=1);

namespace Sod\DatabaseConnection;

use PDO;

interface DatabaseConnectionInterface
{
    /**
     * create a new database connection
     *
     * @return PDO
     */
    public function open(): PDO;

    /**
     * close a database connection
     *
     * @return void
     */
    public function close(): void;
}
