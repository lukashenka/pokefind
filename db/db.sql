CREATE TABLE pokemon
(
    id INT(11) PRIMARY KEY NOT NULL,
    name VARCHAR(255) NOT NULL,
    created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    pokeuid VARCHAR(255)
);
CREATE UNIQUE INDEX pokemon_name_uindex ON pokemon (name);
CREATE INDEX pokemon_pokeuid_index ON pokemon (pokeuid);




CREATE TABLE pokemon_location
(
    id INT(11) PRIMARY KEY NOT NULL,
    pokemon_id INT(11) NOT NULL,
    lat FLOAT(10,6),
    lng FLOAT(10,6),
    created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    expired DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL
);
CREATE INDEX pokemon_created ON pokemon_location (created);
CREATE INDEX pokemon_id ON pokemon_location (pokemon_id);
CREATE INDEX pokemon_location_lat_lng_index ON pokemon_location (lat, lng);

CREATE TABLE location_for_update
(
    id INT(11) PRIMARY KEY NOT NULL,
    lat FLOAT(10,6),
    lng FLOAT(10,6),
    blocked TINYINT(1) DEFAULT '0',
    created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL
);
CREATE INDEX created ON location_for_update (created, blocked);
CREATE UNIQUE INDEX location_for_update_lat_lng_uindex ON location_for_update (lat, lng);