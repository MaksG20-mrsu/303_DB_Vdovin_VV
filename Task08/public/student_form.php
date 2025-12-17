<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$db = getDB();
$groups = getGroups($db);

// Получение данных студента для редактирования
$student = null;
if (isset($_GET['id'])) {
    $student = getStudent($db, $_GET['id']);
    if (!$student) {
        die('Студент не найден');
    }
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        ':last_name' => $_POST['last_name'],
        ':first_name' => $_POST['first_name'],
        ':middle_name' => $_POST['middle_name'] ?? null,
        ':group_id' => $_POST['group_id'],
        ':gender' => $_POST['gender'],
        ':birth_date' => $_POST['birth_date'] ?: null
    ];
    
    if ($student) {
        // Обновление
        $sql = 'UPDATE students SET 
                last_name = :last_name,
                first_name = :first_name,
                middle_name = :middle_name,
                group_id = :group_id,
                gender = :gender,
                birth_date = :birth_date
                WHERE id = :id';
        $data[':id'] = $student['id'];
    } else {
        // Добавление
        $sql = 'INSERT INTO students 
                (last_name, first_name, middle_name, group_id, gender, birth_date)
                VALUES (:last_name, :first_name, :middle_name, :group_id, :gender, :birth_date)';
    }
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
        redirect('index.php');
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $student ? 'Редактирование' : 'Добавление' ?> студента</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1><?= $student ? 'Редактирование студента' : 'Добавление нового студента' ?></h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="post" class="form">
            <div class="form-group">
                <label>Фамилия:</label>
                <input type="text" name="last_name" 
                       value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Имя:</label>
                <input type="text" name="first_name" 
                       value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Отчество:</label>
                <input type="text" name="middle_name" 
                       value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Группа:</label>
                <select name="group_id" required>
                    <option value="">Выберите группу</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['id'] ?>"
                            <?= ($student['group_id'] ?? '') == $group['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($group['group_number']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="М" 
                            <?= ($student['gender'] ?? '') == 'М' ? 'checked' : '' ?> required> Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="Ж"
                            <?= ($student['gender'] ?? '') == 'Ж' ? 'checked' : '' ?>> Женский
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Дата рождения:</label>
                <input type="date" name="birth_date" 
                       value="<?= $student['birth_date'] ?? '' ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Сохранить</button>
                <a href="index.php" class="btn cancel">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>