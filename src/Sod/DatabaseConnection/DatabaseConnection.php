<?php

declare(strict_types=1);

namespace Sod\DatabaseConnection;

use PDO;
use PDOException;
use Sod\DatabaseConnection\DatabaseConnectionInterface;
use Sod\DatabaseConnection\Exception\DatabaseConnectionException;

class DatabaseConnection implements DatabaseConnectionInterface
{
    /**
     * @var PDO
     */
    protected PDO $dbh;

    /**
     * @var array
     */
    protected array $credentials;

    /**
     * __construct
     *
     * @param  array $credentials
     * @return void
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @inheritdoc
     */
    public function open(): PDO
    {
        try {
            $params = [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->dbh = new PDO(
                $this->credentials['dsn'],
                $this->credentials['username'],
                $this->credentials['password'],
                $params
            );
            return $this->dbh;
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(
                $e->getMessage(),
                (int) $e->getCode()
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function close(): void
    {
        $this->dbh = null;
    }
}
