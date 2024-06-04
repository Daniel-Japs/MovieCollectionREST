-- Benutzer-Tabelle
CREATE TABLE users (
                       user_id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL
);

-- Serien-Tabelle
CREATE TABLE series (
                        series_id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(100) NOT NULL,
                        seasons INT NOT NULL,
                        genre VARCHAR(50),
                        platform VARCHAR(50),
						rating INT,
                        user_id INT,
                        FOREIGN KEY (user_id) REFERENCES users(user_id)
);


-- Benutzer-Inserts
INSERT INTO users (username, password) VALUES
                                           ('user1', 'user1'),
                                           ('user2', 'user2');

-- Serien-Inserts
INSERT INTO series (title, seasons, genre, platform, user_id) VALUES
                                                                    ('Game of Thrones', 8, 'Fantasy', 'HBO', 1),
                                                                    ('Breaking Bad', 5, 'Drama', 'Netflix', 2),
                                                                    ('Stranger Things', 3, 'Sci-Fi', 'Netflix', 2),
                                                                    ('The Office', 9, 'Comedy', 'Netflix', 2),
                                                                    ('Friends', 10, 'Comedy', 'Netflix', 1),
                                                                    ('The Witcher', 2, 'Fantasy', 'Netflix', 2),
                                                                    ('Breaking Bad', 5, 'Drama', 'AMC', 1),
                                                                    ('The Mandalorian', 3, 'Science Fiction', 'Disney+', 1),
                                                                    ('The Crown', 6, 'Historical Drama', 'Netflix', 2),
                                                                    ('Westworld', 4, 'Science Fiction', 'HBO', 2),
                                                                    ('Peaky Blinders', 6, 'Historical Drama', 'BBC One', 2),
                                                                    ('The Boys', 3, 'Superhero', 'Amazon Prime', 1);