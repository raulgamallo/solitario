CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE users (
	id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
	email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(64) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    pfp VARCHAR(255) NULL
);

CREATE TABLE games (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_uuid UUID NOT NULL,
    movements BIGINT NOT NULL,
    started TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    finished TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- CREATE OR REPLACE FUNCTION ranking()
-- RETURNS TABLE(user_uuid UUID, total_movements BIGINT) AS $$
-- BEGIN
--     RETURN QUERY
--     SELECT
--         u.id,
--         SUM(g.movements)::BIGINT AS total_movements
--     FROM
--         users u
--     JOIN
--         games g ON u.id = g.user_uuid
--     GROUP BY
--         u.id
--     ORDER BY
--         total_movements ASC;
-- END;
-- $$ LANGUAGE plpgsql;
