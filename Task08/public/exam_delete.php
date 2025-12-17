<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

if (!isset($_GET['id'])) {
    die('Не указан ID экзамена');
}

$id = $_GET['id'];

// Подтверждение удаления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam = getExam($db, $id);
    try {
        $stmt = $db->prepare('DELETE FROM exams WHERE id = ?');
        $stmt->execute([$id]);
        redirect('exams.php?student_id=' . $exam['student_id']);
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

$exam = getExam($db, $id);
if (!$exam) {
    die('Запись не найдена');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удаление результата экзамена</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Удаление результата экзамена</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="warning">
            <p>Вы действительно хотите удалить результат экзамена:</p>
            <p>Студент: <strong><?= htmlspecialchars($exam['last_name']) ?></strong></p>
            <p>Дисциплина: <?= htmlspecialchars($exam['subject_name']) ?></p>
            <p>Дата: <?= $exam['exam_date'] ?>, Оценка: <?= $exam['grade'] ?></p>
        </div>
        
        <form method="post" class="form">
            <div class="form-actions">
                <button type="submit" class="btn delete">Удалить</button>
                <a href="exams.php?student_id=<?= $exam['student_id'] ?>" class="btn cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>