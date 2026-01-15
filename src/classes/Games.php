<?php
require_once __DIR__ . '/Postgres.php';

class Game
{
    final public function __construct(private string $uuid, private int $milliseconds, private int $movements, private User $user)
    {
        throw new \Exception('Not implemented');
    }
}

class GameHistory
{
    final public function __construct(private array $games)
    {
        throw new \Exception('Not implemented');
    }
}

class GameStats
{
    public function __construct(private Postgres $db)
    {}

    public function getLastGames(string $userUuid, int $limit = 10): array {
        $this->db->connect();
        $userUuid = $this->db->escapeLiteral($userUuid);
        // Seleccionamos las últimas partidas, pero luego las invertimos para que el gráfico vaya de pasado a futuro
        $sql = "SELECT movements, started, finished, 
                EXTRACT(EPOCH FROM (finished - started)) as duration_seconds
                FROM games 
                WHERE user_uuid = '$userUuid' 
                ORDER BY started DESC 
                LIMIT $limit";
        
        $result = $this->db->query($sql);
        $this->db->disconnect();
        
        return $result ? array_reverse($result) : [];
    }

    public function getGamesPerDay(string $userUuid, int $days = 7): array {
        $this->db->connect();
        $userUuid = $this->db->escapeLiteral($userUuid);
        $sql = "SELECT TO_CHAR(started, 'YYYY-MM-DD') as game_date, COUNT(*) as count 
                FROM games 
                WHERE user_uuid = '$userUuid' 
                  AND started > NOW() - INTERVAL '$days days' 
                GROUP BY game_date 
                ORDER BY game_date ASC";
        
        $result = $this->db->query($sql);
        $this->db->disconnect();
        
        return $result ?: [];
    }
}

