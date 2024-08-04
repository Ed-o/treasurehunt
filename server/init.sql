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
(1, 'place', 'The English Market', 'A historic food market offering a variety of local produce and goods but with a foreign name.'),
(2, 'item', '', 'Todays Newspaper: "A photo of today’s news in print, a snapshot of current events."'),
(3, 'place', 'St. Fin Barres Cathedral', 'A stunning Gothic Revival cathedral with impressive architecture.'),
(4, 'item', '', 'A Cork City Souvenir: "Seek a small token from Cork, a keepsake to mark your journey."'),
(5, 'place', 'Shandon Bells and Tower (St. Annes Church)', 'Known for its iconic clock, the Four-Faced Liar.'),
(6, 'item', '', 'A Pub Coaster: "Collect a coaster from a local pub, a memento of Cork’s vibrant social scene."');

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
(1, false, "Team-1", "120363320103650944@g.us", 1),
(2, false, "Team-2", "120363318738182435@g.us", 5),
(11, true, "Team-Test-1", "120363317768484698@g.us", 1),
(12, true, "Team-Test-2", "120363303008399256@g.us", 5);





