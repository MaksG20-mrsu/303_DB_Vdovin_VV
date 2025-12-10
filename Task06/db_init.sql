-- Создание таблицы мастеров
CREATE TABLE IF NOT EXISTS masters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    phone TEXT,
    email TEXT,
    hire_date DATE NOT NULL DEFAULT CURRENT_DATE,
    dismissal_date DATE,
    salary_percent REAL NOT NULL DEFAULT 0.3 CHECK (salary_percent >= 0 AND salary_percent <= 1),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы услуг
CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    duration_minutes INTEGER NOT NULL CHECK (duration_minutes > 0),
    price REAL NOT NULL CHECK (price >= 0),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы записей
CREATE TABLE IF NOT EXISTS appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    client_first_name TEXT NOT NULL,
    client_last_name TEXT NOT NULL,
    client_phone TEXT NOT NULL,
    client_email TEXT,
    car_model TEXT NOT NULL,
    car_license_plate TEXT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status TEXT NOT NULL DEFAULT 'scheduled' CHECK (status IN ('scheduled', 'completed', 'cancelled', 'no_show')),
    total_price REAL NOT NULL DEFAULT 0 CHECK (total_price >= 0),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (master_id) REFERENCES masters (id) ON DELETE RESTRICT
);

-- Создание таблицы связи многие-ко-многим между записями и услугами
CREATE TABLE IF NOT EXISTS appointment_services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
    price_per_unit REAL NOT NULL CHECK (price_per_unit >= 0),
    FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services (id) ON DELETE RESTRICT,
    UNIQUE (appointment_id, service_id)
);

-- Создание таблицы выполненных работ
CREATE TABLE IF NOT EXISTS completed_works (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL UNIQUE,
    master_id INTEGER NOT NULL,
    completion_date DATE NOT NULL DEFAULT CURRENT_DATE,
    completion_time TIME NOT NULL DEFAULT CURRENT_TIME,
    actual_duration_minutes INTEGER NOT NULL CHECK (actual_duration_minutes > 0),
    notes TEXT,
    total_earnings REAL NOT NULL CHECK (total_earnings >= 0),
    master_earnings REAL NOT NULL CHECK (master_earnings >= 0),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON DELETE RESTRICT,
    FOREIGN KEY (master_id) REFERENCES masters (id) ON DELETE RESTRICT
);

-- Создание индексов для оптимизации запросов
CREATE INDEX IF NOT EXISTS idx_appointments_master_date ON appointments (master_id, appointment_date);
CREATE INDEX IF NOT EXISTS idx_appointments_date_status ON appointments (appointment_date, status);
CREATE INDEX IF NOT EXISTS idx_masters_active ON masters (is_active);
CREATE INDEX IF NOT EXISTS idx_services_active ON services (is_active);
CREATE INDEX IF NOT EXISTS idx_completed_works_date ON completed_works (completion_date);
CREATE INDEX IF NOT EXISTS idx_completed_works_master ON completed_works (master_id, completion_date);

-- Заполнение тестовыми данными

-- Мастера
INSERT INTO masters (first_name, last_name, phone, email, salary_percent) VALUES
('Иван', 'Петров', '+7-911-123-45-67', 'ivan.petrov@sto.ru', 0.35),
('Мария', 'Сидорова', '+7-912-234-56-78', 'maria.sidorova@sto.ru', 0.4),
('Алексей', 'Козлов', '+7-913-345-67-89', 'alexey.kozlov@sto.ru', 0.3),
('Ольга', 'Николаева', '+7-914-456-78-90', 'olga.nikolaeva@sto.ru', 0.38);

-- Услуги
INSERT INTO services (name, description, duration_minutes, price) VALUES
('Замена масла', 'Полная замена моторного масла и масляного фильтра', 60, 2500),
('Замена тормозных колодок', 'Замена передних или задних тормозных колодок', 90, 4000),
('Развал-схождение', 'Регулировка углов установки колес', 120, 3500),
('Диагностика двигателя', 'Компьютерная диагностика двигателя', 45, 2000),
('Замена свечей зажигания', 'Замена комплекта свечей зажигания', 60, 1800),
('Балансировка колес', 'Балансировка 4-х колес', 75, 2200);

-- Записи
INSERT INTO appointments (master_id, client_first_name, client_last_name, client_phone, car_model, appointment_date, appointment_time, total_price) VALUES
(1, 'Дмитрий', 'Иванов', '+7-921-111-22-33', 'Toyota Camry', DATE('now', '+1 day'), '10:00', 4500),
(2, 'Елена', 'Смирнова', '+7-921-222-33-44', 'Honda Civic', DATE('now', '+1 day'), '11:30', 7500),
(1, 'Сергей', 'Кузнецов', '+7-921-333-44-55', 'BMW X5', DATE('now', '+2 days'), '14:00', 8200);

-- Услуги в записях
INSERT INTO appointment_services (appointment_id, service_id, quantity, price_per_unit) VALUES
(1, 1, 1, 2500), -- Замена масла
(1, 6, 1, 2000), -- Балансировка колес
(2, 2, 1, 4000), -- Замена тормозных колодок
(2, 4, 1, 2000), -- Диагностика двигателя
(2, 5, 1, 1500), -- Замена свечей зажигания
(3, 3, 1, 3500), -- Развал-схождение
(3, 6, 1, 2200), -- Балансировка колес
(3, 1, 1, 2500); -- Замена масла

-- Выполненные работы
INSERT INTO completed_works (appointment_id, master_id, actual_duration_minutes, total_earnings, master_earnings) VALUES
(1, 1, 70, 4500, 1575); -- 4500 * 0.35 = 1575