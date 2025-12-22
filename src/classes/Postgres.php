<?php
class Postgres
{
    private readonly string $host;
    private readonly int $port;
    private readonly string $user;
    private readonly string $password;
    private readonly string $dbname;
    private readonly string $sslmode;
    private $connection;

    public function __construct(string $host, string $user, string $database, string $password, int $port = 5432, string $sslmode = "prefer")
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->dbname = $database;
        $this->sslmode = $sslmode;
    }

    public function connect()
    {
        $connectionString = "host={$this->host} port={$this->port} dbname={$this->dbname} user={$this->user} password={$this->password} sslmode={$this->sslmode}";
        $this->connection = pg_connect($connectionString);

        if (!$this->connection) {
            throw new Exception("Connection to PostgreSQL failed: " . pg_last_error());
        }
    }

    public function disconnect()
    {
        if ($this->connection) {
            pg_close($this->connection);
        }
    }

    public function query($sql)
    {
        if (!$this->connection) {
            throw new Exception("No active database connection.");
        }

        $result = pg_query($this->connection, $sql);

        if (!$result) {
            throw new Exception("Query failed: " . pg_last_error($this->connection));
        }

        return pg_fetch_all($result);
    }
}

$postgres = new Postgres("postgres", getenv("POSTGRES_USER"), getenv("POSTGRES_DB"), getenv("POSTGRES_PASSWORD"));
$postgres->connect();