<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

if (!isset($_GET['id'])) {
    die('Не указан ID студента');
}

$id = $_GET['id'];

// Подтверждение удаления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $db->prepare('DELETE FROM students WHERE id = ?');
        $stmt->execute([$id]);
        redirect('index.php');
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

$student = getStudent($db, $id);
if (!$student) {
    die('Студент не найден');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удаление студента</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Удаление студента</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="warning">
            <p>Вы действительно хотите удалить студента:</p>
            <p><strong><?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?></strong></p>
            <p>Группа: <?= htmlspecialchars($student['group_number']) ?></p>
            <p>Все результаты экзаменов этого студента также будут удалены!</p>
        </div>
        
        <form method="post" class="form">
            <div class="form-actions">
                <button type="submit" class="btn delete">Удалить</button>
                <a href="index.php" class="btn cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>