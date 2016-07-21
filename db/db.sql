CREATE TABLE pokemon
(
    id INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    pokeuid VARCHAR(255),
    new_column INT(11)
);
CREATE UNIQUE INDEX pokemon_name_uindex ON pokemon (name);
CREATE INDEX pokemon_pokeuid_index ON pokemon (pokeuid);
CREATE TABLE pokemon_location
(
    id INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    pokemon_id INT(11) NOT NULL,
    lat FLOAT(12,8),
    lng FLOAT(12,8),
    created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    expired DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL
);
CREATE INDEX pokemon_created ON pokemon_location (created);
CREATE INDEX pokemon_id ON pokemon_location (pokemon_id);
CREATE INDEX pokemon_location_lat_lng_index ON pokemon_location (lat, lng);
CREATE TABLE location_for_update
(
    id INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    lat FLOAT(12,8),
    lng FLOAT(12,8),
    blocked TINYINT(1) DEFAULT '0',
    created DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    user_session_id INT(11) DEFAULT '0'
);
CREATE INDEX created ON location_for_update (created, blocked);
CREATE UNIQUE INDEX location_for_update_lat_lng_blocked_uindex ON location_for_update (lat, lng, blocked);
CREATE INDEX location_for_update_user_session_id_index ON location_for_update (user_session_id);
CREATE TABLE user_sessions
(
    id INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    guid VARCHAR(255) NOT NULL,
    ip INT(10) DEFAULT '0' NOT NULL,
    ip_string VARCHAR(20) NOT NULL,
    created DATETIME DEFAULT '0000-00-00 00:00:00',
    updated DATETIME DEFAULT '0000-00-00 00:00:00'
);
CREATE INDEX created ON user_sessions (created);
CREATE INDEX ip ON user_sessions (ip);
CREATE INDEX user_sessions_guid_index ON user_sessions (guid);
CREATE TABLE user_session_track
(
    id INT(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_session_id INT(11) DEFAULT '11',
    lat FLOAT(12,8) DEFAULT '0.00000000' NOT NULL,
    lng FLOAT(12,8) DEFAULT '0.00000000',
    updated DATETIME DEFAULT '0000-00-00 00:00:00'
);
CREATE INDEX user_session_track_lat_lng_index ON user_session_track (lat, lng);
CREATE INDEX user_session_track_updated_index ON user_session_track (updated);
CREATE INDEX user_session_track_user_session_id_index ON user_session_track (user_session_id);