CREATE TABLE movie_genres
(
    id INT NOT NULL AUTO_INCREMENT,
    movie_id INT NOT NULL,
    genre_id INT NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_movie_genre (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
