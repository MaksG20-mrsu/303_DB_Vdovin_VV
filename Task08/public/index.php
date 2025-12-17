<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

// Получаем группы для фильтра
$groups = getGroups($db);

// Применяем фильтр по группе
$group_filter = $_GET['group'] ?? null;
$students = getStudents($db, $group_filter);

// Сортируем студентов
usort($students, function($a, $b) {
    $cmp = strcmp($a['group_number'], $b['group_number']);
    if ($cmp === 0) {
        return strcmp($a['last_name'], $b['last_name']);
    }
    return $cmp;
});
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Список студентов</h1>
        
        <!-- Фильтр по группе -->
        <form method="get" action="" class="filter-form">
            <select name="group" onchange="this.form.submit()">
                <option value="">Все группы</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?= $group['id'] ?>" 
                        <?= ($group_filter == $group['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($group['group_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Применить</button>
            <a href="?" class="btn">Сбросить</a>
        </form>
        
        <!-- Таблица студентов -->
        <table>
            <thead>
                <tr>
                    <th>ФИО</th>
                    <th>Группа</th>
                    <th>Пол</th>
                    <th>Дата рождения</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Нет данных</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['last_name'] . ' ' . 
                            $student['first_name'] . ' ' . 
                            ($student['middle_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($student['group_number']) ?></td>
                        <td><?= htmlspecialchars($student['gender'] ?? '') ?></td>
                        <td><?= $student['birth_date'] ?? '' ?></td>
                        <td class="actions">
                            <a href="student_form.php?id=<?= $student['id'] ?>" class="btn edit">Редактировать</a>
                            <a href="exams.php?student_id=<?= $student['id'] ?>" class="btn exams">Результаты экзаменов</a>
                            <a href="student_delete.php?id=<?= $student['id'] ?>" class="btn delete" 
                               onclick="return confirm('Удалить студента?')">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="footer">
            <a href="student_form.php" class="btn add">Добавить студента</a>
        </div>
    </div>
</body>
</html>