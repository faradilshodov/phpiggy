<?php

declare(strict_types=1);

namespace Framework;

use PDO, PDOException;

class database
{
    private PDO $connection;

    public function __construct(
        string $driver,
        array $config,
        string $username,
        string $password
    ) {
        $config = http_build_query(data: $config, arg_separator: ';');

        $dsn = "{$driver}:{$config}";

        try {
            $this->connection = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            die("Connection failed");
        }
    }

    public function query(string $query)
    {
        $this->connection->query($query);
    }
}
