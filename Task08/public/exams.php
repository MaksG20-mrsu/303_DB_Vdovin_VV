<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

if (!isset($_GET['student_id'])) {
    die('Не указан студент');
}

$student_id = $_GET['student_id'];
$student = getStudent($db, $student_id);
$exams = getStudentExams($db, $student_id);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты экзаменов</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Результаты экзаменов студента</h1>
        <h2><?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?></h2>
        <p>Группа: <?= htmlspecialchars($student['group_number']) ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>Дисциплина</th>
                    <th>Дата экзамена</th>
                    <th>Оценка</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($exams)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Нет данных</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= htmlspecialchars($exam['subject_name']) ?></td>
                        <td><?= $exam['exam_date'] ?></td>
                        <td><?= $exam['grade'] ?></td>
                        <td class="actions">
                            <a href="exam_form.php?id=<?= $exam['id'] ?>" class="btn edit">Редактировать</a>
                            <a href="exam_delete.php?id=<?= $exam['id'] ?>" class="btn delete" 
                               onclick="return confirm('Удалить запись?')">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="footer">
            <a href="exam_form.php?student_id=<?= $student_id ?>" class="btn add">Добавить результат экзамена</a>
            <a href="index.php" class="btn">Назад к списку студентов</a>
        </div>
    </div>
</body>
</html>