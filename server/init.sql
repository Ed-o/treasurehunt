# CREATE USER 'username'@'%' IDENTIFIED BY 'password';
# GRANT ALL PRIVILEGES ON database.* TO 'username'@'%' ;

###
### Clues
###

DROP TABLE records ;

CREATE TABLE IF NOT EXISTS records (
    id INT PRIMARY KEY,
    what TEXT NOT NULL,
    place TEXT,
    text TEXT NOT NULL
);

INSERT INTO records (id, what, place, text) VALUES 
(1, 'place', 'Test1', 'Test 1'),
(2, 'item', '', 'Test 2');

###
### Teams
###

DROP TABLE teams ;

CREATE TABLE IF NOT EXISTS teams (
    id INT PRIMARY KEY,
    state BOOLEAN,
    name TEXT NOT NULL,
    number TEXT NOT NULL,
    clue INT
);

INSERT INTO teams (id, state, name, number, clue) VALUES
(1, false, "Team-1", "1234567890@g.us", 1);






