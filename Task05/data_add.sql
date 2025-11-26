-- Добавление пяти новых пользователей (себя и четырех соседей по группе)
INSERT OR IGNORE INTO users (email, first_name, last_name, gender_id) 
VALUES 
('ildar.adeev@mail.com', 'Ильдар', 'Адеев', (SELECT id FROM genders WHERE code = 'M')),
('vladislav.vdovin@mail.com', 'Владислав', 'Вдовин', (SELECT id FROM genders WHERE code = 'M')),
('evgeny.golikov@mail.com', 'Евгений', 'Голиков', (SELECT id FROM genders WHERE code = 'M')),
('maxim.zhivaev@mail.com', 'Максим', 'Живаев', (SELECT id FROM genders WHERE code = 'M')),
('georgy.vlasov@mail.com', 'Георгий', 'Власов', (SELECT id FROM genders WHERE code = 'M'));

-- Добавление трех новых фильмов разных жанров
INSERT OR IGNORE INTO movies (title, release_year, genre_id, description) 
VALUES 
('Зеленая миля', 1999, (SELECT id FROM genres WHERE name = 'Драма'), 'Душевная драма о чудесах в тюрьме смертников'),
('Мальчишник в Вегасе', 2009, (SELECT id FROM genres WHERE name = 'Комедия'), 'Комедийная история о незабываемом мальчишнике'),
('Матрица', 1999, (SELECT id FROM genres WHERE name = 'Фантастика'), 'Культовая фантастика о реальности и иллюзиях');

-- Добавление трех новых отзывов от себя
INSERT OR IGNORE INTO reviews (user_id, movie_id, rating, review_text) 
VALUES 
(
    (SELECT id FROM users WHERE email = 'vladislav.vdovin@mail.com'),
    (SELECT id FROM movies WHERE title = 'Зеленая миля'),
    5.0,
    'Невероятно трогательный фильм! Том Хэнкс и Майкл Кларк Дункан создали незабываемые образы. Плакал в нескольких моментах.'
),
(
    (SELECT id FROM users WHERE email = 'vladislav.vdovin@mail.com'),
    (SELECT id FROM movies WHERE title = 'Мальчишник в Вегасе'),
    4.5,
    'Очень смешная комедия! Идеальный подбор актеров и запоминающиеся шутки. Пересматривал несколько раз.'
),
(
    (SELECT id FROM users WHERE email = 'vladislav.vdovin@mail.com'),
    (SELECT id FROM movies WHERE title = 'Матрица'),
    4.8,
    'Революционный для своего времени фильм. Потрясающая философская концепция и новаторские спецэффекты. Must-see!'
);