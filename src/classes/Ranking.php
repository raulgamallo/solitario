<?php
require_once __DIR__ . '/Postgres.php';

class Ranking
{
    private Postgres $postgres;

    public function __construct(Postgres $postgres)
    {
        $this->postgres = $postgres;
    }

    public function getRankings(string $sortBy = 'time', string $order = 'ASC'): array
    {
        $allowedSorts = ['time', 'movements'];
        $allowedOrders = ['ASC', 'DESC'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'time';
        }
        if (!in_array($order, $allowedOrders)) {
            $order = 'ASC';
        }

        $orderBy = match ($sortBy) {
            'time' => '(finished - started)',
            'movements' => 'movements',
        };

        $sql = "
            SELECT 
                u.username, 
                g.movements, 
                g.started, 
                g.finished,
                EXTRACT(EPOCH FROM (g.finished - g.started)) AS duration_seconds,
                to_char((g.finished - g.started), 'HH24:MI:SS') as duration_formatted
            FROM games g
            INNER JOIN users u ON g.user_uuid = u.uuid
            ORDER BY $orderBy $order
            LIMIT 100
        ";

        $this->postgres->connect();
        try {
             $data = $this->postgres->query($sql);
             return $data ?: [];
        } catch (Exception $e) {
            // Log error or handle it
            return [];
        } finally {
            $this->postgres->disconnect();
        }
    }
}
