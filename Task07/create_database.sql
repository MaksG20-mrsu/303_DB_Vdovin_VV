-- Создание таблицы групп
CREATE TABLE IF NOT EXISTS groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    number INTEGER NOT NULL UNIQUE,
    direction TEXT NOT NULL,
    graduation_year INTEGER NOT NULL
);

-- Создание таблицы студентов
CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL,
    last_name TEXT NOT NULL,
    first_name TEXT NOT NULL,
    middle_name TEXT,
    gender TEXT CHECK(gender IN ('М', 'Ж')),
    birth_date TEXT NOT NULL,
    student_id TEXT NOT NULL UNIQUE,
    FOREIGN KEY (group_id) REFERENCES groups(id)
);

-- Вставка тестовых данных групп
INSERT OR IGNORE INTO groups (number, direction, graduation_year) VALUES
(101, 'Информатика и вычислительная техника', 2025),
(102, 'Программная инженерия', 2025),
(103, 'Прикладная математика', 2024),
(201, 'Системный анализ', 2025),
(202, 'Информационная безопасность', 2026);

-- Вставка тестовых данных студентов
INSERT OR IGNORE INTO students (group_id, last_name, first_name, middle_name, gender, birth_date, student_id) VALUES
(1, 'Иванов', 'Иван', 'Иванович', 'М', '2002-05-15', '101001'),
(1, 'Петров', 'Петр', 'Петрович', 'М', '2003-02-20', '101002'),
(1, 'Сидорова', 'Мария', 'Ивановна', 'Ж', '2002-11-10', '101003'),
(2, 'Кузнецов', 'Алексей', 'Сергеевич', 'М', '2001-08-25', '102001'),
(2, 'Смирнова', 'Анна', 'Владимировна', 'Ж', '2003-01-30', '102002'),
(3, 'Попов', 'Дмитрий', 'Александрович', 'М', '2000-12-05', '103001'),
(4, 'Новикова', 'Елена', 'Сергеевна', 'Ж', '2002-07-18', '201001'),
(4, 'Морозов', 'Сергей', 'Викторович', 'М', '2003-03-22', '201002'),
(5, 'Волков', 'Андрей', 'Николаевич', 'М', '2004-09-14', '202001');