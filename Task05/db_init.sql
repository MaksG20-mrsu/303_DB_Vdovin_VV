-- Создание таблицы для полов пользователей (справочник)
CREATE TABLE IF NOT EXISTS genders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(1) NOT NULL UNIQUE,
    name VARCHAR(10) NOT NULL UNIQUE
);

-- Создание таблицы для жанров фильмов (справочник)
CREATE TABLE IF NOT EXISTS genres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender_id INTEGER NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gender_id) REFERENCES genders(id) ON DELETE RESTRICT,
    CHECK (email LIKE '%_@_%.__%')
);

-- Создание таблицы фильмов
CREATE TABLE IF NOT EXISTS movies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    release_year INTEGER NOT NULL,
    genre_id INTEGER NOT NULL,
    description TEXT,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE RESTRICT,
    CHECK (release_year BETWEEN 1888 AND 2030) -- Исправленная строка
);

-- Создание таблицы отзывов
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    movie_id INTEGER NOT NULL,
    rating DECIMAL(2,1) NOT NULL DEFAULT 0,
    review_text TEXT,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE RESTRICT,
    CHECK (rating >= 0 AND rating <= 5),
    UNIQUE(user_id, movie_id)
);

-- Заполнение справочника полов
INSERT OR IGNORE INTO genders (code, name) VALUES 
('M', 'Мужской'),
('F', 'Женский');

-- Заполнение справочника жанров
INSERT OR IGNORE INTO genres (name) VALUES 
('Драма'),
('Комедия'),
('Боевик'),
('Фантастика'),
('Ужасы'),
('Романтика'),
('Триллер'),
('Детектив'),
('Приключения'),
('Аниме'),
('Мультфильм'),
('Документальный');

-- Создание индексов для оптимизации запросов
CREATE INDEX IF NOT EXISTS idx_users_last_name ON users(last_name);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_movies_title ON movies(title);
CREATE INDEX IF NOT EXISTS idx_movies_release_year ON movies(release_year);
CREATE INDEX IF NOT EXISTS idx_movies_genre ON movies(genre_id);
CREATE INDEX IF NOT EXISTS idx_reviews_user_id ON reviews(user_id);
CREATE INDEX IF NOT EXISTS idx_reviews_movie_id ON reviews(movie_id);
CREATE INDEX IF NOT EXISTS idx_reviews_rating ON reviews(rating);