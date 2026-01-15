CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE users (
	uuid UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
	email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(64) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    pfp VARCHAR(255) NULL
);

CREATE TABLE games (
    uuid UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_uuid UUID NOT NULL,
    movements BIGINT NOT NULL,
    started TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    finished TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE OR REPLACE FUNCTION registro_usuario(
    r_email VARCHAR(255),
    r_username VARCHAR(64),
    r_password_hash VARCHAR(255),
    r_pfp VARCHAR(255)

)
RETURNS UUID AS $$
DECLARE
    new_user_id UUID;
BEGIN
    INSERT INTO users (email, username, password_hash, pfp)
    VALUES (r_email, r_username, r_password_hash, r_pfp)
    RETURNING id INTO new_user_id;
    
    RETURN new_user_id;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION guardar_partida(
    g_user_uuid UUID,
    g_movements BIGINT,
    g_started TIMESTAMPTZ DEFAULT NOW(),
    g_finished TIMESTAMPTZ DEFAULT NOW()
)
RETURNS UUID AS $$
DECLARE
    new_game_id UUID;
BEGIN
    INSERT INTO games (user_uuid, movements, started, finished)
    VALUES (g_user_uuid, g_movements, g_started, g_finished)
    RETURNING id INTO new_game_id;

    RETURN new_game_id;
END;
$$ LANGUAGE plpgsql;
