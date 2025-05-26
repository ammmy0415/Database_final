
-- 建立 Users 使用者資料表
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('User', 'Admin') NOT NULL DEFAULT 'User',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 建立 Movies 電影資料表
CREATE TABLE Movies (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    director VARCHAR(100),
    genre VARCHAR(50),
    release_date DATE,
    summary TEXT,
    poster_url VARCHAR(255)
);

-- 建立 Reviews 影評資料表
CREATE TABLE Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    rating FLOAT,
    review_text TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id)
);

-- 建立 Genres 類型表
CREATE TABLE Genres (
    genre_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- 建立 Actors 演員表
CREATE TABLE Actors (
    actor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- 建立 MovieStills 劇照表
CREATE TABLE MovieStills (
    still_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT,
    image_url VARCHAR(255),
    description TEXT,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id)ON DELETE CASCADE
)ENGINE=InnoDB;

-- 建立 MovieFashion 穿搭推薦表
CREATE TABLE MovieFashion (
    fashion_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT,
    look_title VARCHAR(50),
    look_image_url VARCHAR(255),
    description TEXT,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id)ON DELETE CASCADE
)ENGINE=InnoDB;

-- 建立 StreamingLinks 串流連結表
CREATE TABLE StreamingLinks (
    link_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT,
    link_title VARCHAR(50),
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id)
);

-- 建立關聯表 mov_actor
CREATE TABLE mov_actor (
    movie_id INT,
    actor_id INT,
    PRIMARY KEY (movie_id, actor_id),
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id),
    FOREIGN KEY (actor_id) REFERENCES Actors(actor_id)
);

-- 建立關聯表 mov_gen
CREATE TABLE mov_gen (
    genre_id INT,
    movie_id INT,
    PRIMARY KEY (genre_id, movie_id),
    FOREIGN KEY (genre_id) REFERENCES Genres(genre_id),
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id)
);

-- 關聯表 mov_still
CREATE TABLE mov_still (
    movie_id INT,
    still_id INT,
    PRIMARY KEY (movie_id, still_id),
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (still_id) REFERENCES MovieStills(still_id) ON DELETE CASCADE
);

-- 關聯表 mov_re
CREATE TABLE mov_re (
    movie_id INT,
    review_id INT,
    user_id INT,
    PRIMARY KEY (movie_id, review_id),
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (review_id) REFERENCES Reviews(review_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- 關聯表 rev_user（實際上已在 Reviews 中表示，可視情況保留）
CREATE TABLE rev_user (
    review_id INT,
    user_id INT,
    PRIMARY KEY (review_id, user_id),
    FOREIGN KEY (review_id) REFERENCES Reviews(review_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- 關聯表 mov_fashion
CREATE TABLE mov_fashion (
    mov_id INT,
    fashion_id INT,
    PRIMARY KEY (mov_id, fashion_id),
    FOREIGN KEY (mov_id) REFERENCES Movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (fashion_id) REFERENCES MovieFashion(fashion_id) ON DELETE CASCADE
);

-- 關聯表 mov_streaming
CREATE TABLE mov_streaming (
    mov_id INT,
    link_id INT,
    PRIMARY KEY (mov_id, link_id),
    FOREIGN KEY (mov_id) REFERENCES Movies(movie_id),
    FOREIGN KEY (link_id) REFERENCES StreamingLinks(link_id)
);
