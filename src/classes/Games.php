<?php
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
