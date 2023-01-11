CREATE TABLE IF NOT EXISTS urls (
    id serial PRIMARY KEY,
    name character varying(255) NOT NULL UNIQUE,
    created_at timestamp
);

