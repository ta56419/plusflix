CREATE TABLE reviews
(
    id INT NOT NULL AUTO_INCREMENT,
    movie_id INT NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT,
    PRIMARY KEY (id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
