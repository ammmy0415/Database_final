
-- 插入 Users 使用者資料
INSERT INTO Users (username, email, password_hash, role) VALUES
('alice', 'alice@example.com', 'hashed_pw1', 'User'),
('bob', 'bob@example.com', 'hashed_pw2', 'User'),
('charlie', 'charlie@example.com', 'hashed_pw3', 'Admin'),
('diana', 'diana@example.com', 'hashed_pw4', 'User'),
('eric', 'eric@example.com', 'hashed_pw5', 'User');

-- 插入 Genres 類型
INSERT INTO Genres (name) VALUES
('Action'), ('Drama'), ('Comedy'), ('Horror'), ('Sci-Fi');

-- 插入 Actors
INSERT INTO Actors (name) VALUES
('Tom Hanks'), ('Scarlett Johansson'), ('Leonardo DiCaprio'), 
('Emma Watson'), ('Keanu Reeves');

-- 插入 Movies
INSERT INTO Movies (title, director, genre, release_date, summary, poster_url) VALUES
('Inception', 'Christopher Nolan', 'Sci-Fi', '2010-07-16', 'Dream invasion and extraction', 'url1.jpg'),
('Titanic', 'James Cameron', 'Drama', '1997-12-19', 'Ship tragedy and romance', 'url2.jpg'),
('The Matrix', 'Wachowskis', 'Action', '1999-03-31', 'Virtual world battle', 'url3.jpg'),
('Avengers', 'Joss Whedon', 'Action', '2012-05-04', 'Superhero alliance', 'url4.jpg'),
('Harry Potter', 'Chris Columbus', 'Fantasy', '2001-11-16', 'Boy wizard at school', 'url5.jpg');

-- 插入 Reviews
INSERT INTO Reviews (user_id, movie_id, rating, review_text) VALUES
(1, 1, 4.5, 'Amazing concept and direction'),
(2, 2, 5.0, 'A timeless classic love story'),
(3, 3, 4.0, 'Mind-bending and iconic'),
(4, 4, 3.5, 'Fun and action-packed'),
(5, 5, 4.8, 'Magical and nostalgic');

-- 插入 MovieStills
INSERT INTO MovieStills (movie_id, image_url, description) VALUES
(1, 'inception1.jpg', 'City folding scene'),
(2, 'titanic1.jpg', 'Jack and Rose on the deck'),
(3, 'matrix1.jpg', 'Neo dodging bullets'),
(4, 'avengers1.jpg', 'Group battle scene'),
(5, 'harrypotter1.jpg', 'Hogwarts castle at night');

-- 插入 MovieFashion
INSERT INTO MovieFashion (movie_id, look_title, look_image_url, description) VALUES
(1, 'Cobb’s Suit', 'suit.jpg', 'Classic dark-toned suit'),
(2, 'Rose’s Dress', 'dress.jpg', 'Elegant Titanic era gown'),
(3, 'Neo’s Coat', 'coat.jpg', 'Iconic black trench coat'),
(4, 'Iron Man Suit', 'armor.jpg', 'Futuristic high-tech armor'),
(5, 'Hogwarts Robe', 'robe.jpg', 'Magical school robe');

-- 插入 StreamingLinks
INSERT INTO StreamingLinks (movie_id, link_title) VALUES
(1, 'Inception Trailer'),
(2, 'Titanic Interview'),
(3, 'Matrix Making-of'),
(4, 'Avengers Highlights'),
(5, 'Harry Potter Behind the Scenes');

-- 插入 mov_actor 關聯
INSERT INTO mov_actor (movie_id, actor_id) VALUES
(1, 3), (2, 1), (3, 5), (4, 2), (5, 4);

-- 插入 mov_gen 關聯
INSERT INTO mov_gen (genre_id, movie_id) VALUES
(5, 1), (2, 2), (1, 3), (1, 4), (3, 5);

-- 插入 mov_still 關聯
INSERT INTO mov_still (movie_id, still_id) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5);

-- 插入 mov_re 關聯
INSERT INTO mov_re (movie_id, review_id, user_id) VALUES
(1, 1, 1), (2, 2, 2), (3, 3, 3), (4, 4, 4), (5, 5, 5);

-- 插入 rev_user 關聯
INSERT INTO rev_user (review_id, user_id) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5);

-- 插入 mov_fashion 關聯
INSERT INTO mov_fashion (mov_id, fashion_id) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5);

-- 插入 mov_streaming 關聯
INSERT INTO mov_streaming (mov_id, link_id) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5);
