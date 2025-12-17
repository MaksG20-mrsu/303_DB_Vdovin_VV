<?php
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function getGroups($db) {
    $stmt = $db->query('SELECT * FROM groups ORDER BY group_number');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudents($db, $group_filter = null) {
    $sql = 'SELECT s.*, g.group_number 
            FROM students s 
            JOIN groups g ON s.group_id = g.id';
    
    if ($group_filter) {
        $sql .= ' WHERE g.id = :group_id';
        $stmt = $db->prepare($sql);
        $stmt->execute([':group_id' => $group_filter]);
    } else {
        $stmt = $db->query($sql);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudent($db, $id) {
    $stmt = $db->prepare('SELECT s.*, g.group_number FROM students s 
                         JOIN groups g ON s.group_id = g.id WHERE s.id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getStudentExams($db, $student_id) {
    $stmt = $db->prepare('SELECT e.*, s.subject_name 
                         FROM exams e 
                         JOIN subjects s ON e.subject_id = s.id 
                         WHERE e.student_id = ? 
                         ORDER BY e.exam_date DESC');
    $stmt->execute([$student_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSubjects($db) {
    $stmt = $db->query('SELECT * FROM subjects ORDER BY course_year, subject_name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExam($db, $id) {
    $stmt = $db->prepare('SELECT e.*, s.subject_name, st.last_name, g.group_number 
                         FROM exams e 
                         JOIN subjects s ON e.subject_id = s.id 
                         JOIN students st ON e.student_id = st.id 
                         JOIN groups g ON st.group_id = g.id 
                         WHERE e.id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>