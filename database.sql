CREATE TABLE IF NOT EXISTS urls (
    id serial PRIMARY KEY,
    name character varying(255) NOT NULL UNIQUE,
    created_at TIMESTAMPTZ DEFAULT Now() 
);
ALTER TABLE urls DROP COLUMN created_at;
ALTER TABLE urls ADD COLUMN created_at timestamp without time zone;
ALTER TABLE urls ALTER COLUMN created_at SET DEFAULT now();


CREATE TABLE IF NOT EXISTS url_checks (
    id serial PRIMARY KEY,
    url_id integer NOT NULL,
    status_code character varying(100),
    h1 character varying(255),
    title character varying(255),
    description text,
    created_at timestamp without time zone DEFAULT Now()
);