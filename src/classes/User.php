<?php
require_once __DIR__ . '/Postgres.php';

final readonly class UserRegisterDTO
{
    public function __construct(
        public string $email,
        public string $username,
        public string $password
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            email: htmlspecialchars(trim($data['email'] ?? '')),
            username: trim($data['username'] ?? ''),
            password: password_hash(trim($data['password'] ?? ''), PASSWORD_BCRYPT)
        );
    }
}

final readonly class UserLoginDTO
{
    public function __construct(
        public string $identifier, // Changed from email to identifier (can be email or username)
        public string $password
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            identifier: htmlspecialchars(trim($data['email'] ?? '')), // Input name is still 'email' in form for now, or change to 'identifier'
            password: trim($data['password'] ?? '')
        );
    }
}

final class User
{
    public function __construct(
        public string $uuid,
        public string $email,
        public string $username,
        public ?string $pfp
    ) {}
}
