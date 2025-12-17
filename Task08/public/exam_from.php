<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();

// Получение студентов и предметов
$students = $db->query('SELECT s.id, s.last_name, s.first_name, g.group_number 
                       FROM students s JOIN groups g ON s.group_id = g.id 
                       ORDER BY g.group_number, s.last_name')->fetchAll();
$subjects = getSubjects($db);

$exam = null;
$student_id = $_GET['student_id'] ?? null;

if (isset($_GET['id'])) {
    $exam = getExam($db, $_GET['id']);
    $student_id = $exam['student_id'];
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        ':student_id' => $_POST['student_id'],
        ':subject_id' => $_POST['subject_id'],
        ':exam_date' => $_POST['exam_date'],
        ':grade' => $_POST['grade']
    ];
    
    if ($exam) {
        $sql = 'UPDATE exams SET 
                student_id = :student_id,
                subject_id = :subject_id,
                exam_date = :exam_date,
                grade = :grade
                WHERE id = :id';
        $data[':id'] = $exam['id'];
    } else {
        $sql = 'INSERT INTO exams (student_id, subject_id, exam_date, grade)
                VALUES (:student_id, :subject_id, :exam_date, :grade)';
    }
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
        redirect('exams.php?student_id=' . $_POST['student_id']);
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $exam ? 'Редактирование' : 'Добавление' ?> результата экзамена</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1><?= $exam ? 'Редактирование результата экзамена' : 'Добавление результата экзамена' ?></h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="post" class="form">
            <div class="form-group">
                <label>Студент:</label>
                <select name="student_id" required <?= $student_id ? 'disabled' : '' ?>>
                    <?php if (!$student_id): ?>
                        <option value="">Выберите студента</option>
                    <?php endif; ?>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            <?= ($student_id == $s['id'] || ($exam && $exam['student_id'] == $s['id'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['last_name'] . ' ' . $s['first_name'] . ' (' . $s['group_number'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($student_id): ?>
                    <input type="hidden" name="student_id" value="<?= $student_id ?>">
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Дисциплина:</label>
                <select name="subject_id" required>
                    <option value="">Выберите дисциплину</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>"
                            <?= ($exam && $exam['subject_id'] == $subject['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subject['subject_name']) ?> (<?= $subject['course_year'] ?> курс)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дата экзамена:</label>
                <input type="date" name="exam_date" 
                       value="<?= $exam['exam_date'] ?? date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Оценка:</label>
                <select name="grade" required>
                    <option value="">Выберите оценку</option>
                    <?php for ($i = 2; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"
                            <?= ($exam && $exam['grade'] == $i) ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Сохранить</button>
                <a href="<?= $student_id ? 'exams.php?student_id=' . $student_id : 'index.php' ?>" 
                   class="btn cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>