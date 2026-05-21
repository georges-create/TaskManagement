<?php
require '../db.php';
require '../vendor/autoload.php'; // For TCPDF, make sure you install it via Composer

use TCPDF;

// ---------------------------------------------
// 1. Generate Timetable PDF
// ---------------------------------------------
function generateTimetablePDF($pdo, $role = 'student', $user_id = null)
{
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('University Scheduler');
    $pdf->SetTitle('Timetable');
    $pdf->SetHeaderData('', 0, 'Your University', 'Timetable');
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();

    // Fetch timetable based on role
    if ($role === 'student') {
        $stmt = $pdo->prepare("SELECT t.day,t.start_time,t.end_time,u.name as unit_name,t.venue 
                               FROM timetable t 
                               JOIN units u ON t.unit_id=u.id 
                               JOIN users s ON s.program=u.program 
                               WHERE s.id=? ORDER BY t.day,t.start_time");
        $stmt->execute([$user_id]);
    } elseif ($role === 'lecturer') {
        $stmt = $pdo->prepare("SELECT t.day,t.start_time,t.end_time,u.name as unit_name,t.venue 
                               FROM timetable t 
                               JOIN units u ON t.unit_id=u.id 
                               WHERE u.lecturer_id=? ORDER BY t.day,t.start_time");
        $stmt->execute([$user_id]);
    } else { // admin - master timetable
        $stmt = $pdo->query("SELECT t.day,t.start_time,t.end_time,u.name as unit_name,t.venue,u.program,l.name as lecturer_name 
                             FROM timetable t 
                             JOIN units u ON t.unit_id=u.id 
                             LEFT JOIN users l ON u.lecturer_id=l.id 
                             ORDER BY t.day,t.start_time");
    }
    $timetable = $stmt->fetchAll();

    // Table header
    $html = '<h2 style="color:#e91e63;">Timetable</h2>';
    $html .= '<table border="1" cellpadding="4">
                <tr style="background-color:#f8bbd0;">
                    <th>Day</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Unit</th>';
    if ($role === 'admin') $html .= '<th>Lecturer</th><th>Program</th>';
    $html .= '<th>Venue</th></tr>';

    foreach ($timetable as $t) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($t['day']) . '</td>
                    <td>' . $t['start_time'] . '</td>
                    <td>' . $t['end_time'] . '</td>
                    <td>' . htmlspecialchars($t['unit_name']) . '</td>';
        if ($role === 'admin') {
            $html .= '<td>' . htmlspecialchars($t['lecturer_name'] ?? '-') . '</td>
                      <td>' . $t['program'] . '</td>';
        }
        $html .= '<td>' . $t['venue'] . '</td></tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('timetable.pdf', 'I'); // Output to browser
}

// ---------------------------------------------
// 2. Create Notification
// ---------------------------------------------
function createNotification($pdo, $message, $user_id = null)
{
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id,message) VALUES (?,?)");
    $stmt->execute([$user_id, $message]);
}

// ---------------------------------------------
// 3. Fetch Notifications for User
// ---------------------------------------------
function fetchNotifications($pdo, $user_id = null)
{
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? OR user_id IS NULL ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$user_id]);
    } else { // Admin
        $stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
    }
    return $stmt->fetchAll();
}

// ---------------------------------------------
// 4. Fetch User Units
// ---------------------------------------------
function getUserUnits($pdo, $user_id, $role)
{
    if ($role === 'student') {
        $stmt = $pdo->prepare("SELECT u.id,u.name,u.program,u.lecturer_id 
                               FROM units u
                               JOIN users s ON s.program=u.program
                               WHERE s.id=?");
        $stmt->execute([$user_id]);
    } elseif ($role === 'lecturer') {
        $stmt = $pdo->prepare("SELECT id,name,program FROM units WHERE lecturer_id=?");
        $stmt->execute([$user_id]);
    }
    return $stmt->fetchAll();
}

// ---------------------------------------------
// 5. Fetch Drop Requests
// ---------------------------------------------
function getDropRequests($pdo, $role, $user_id = null)
{
    if ($role === 'student') {
        $stmt = $pdo->prepare("SELECT dr.*, u.name as unit_name FROM drop_requests dr 
                               JOIN units u ON dr.unit_id=u.id
                               WHERE dr.user_id=? ORDER BY dr.created_at DESC");
        $stmt->execute([$user_id]);
    } elseif ($role === 'lecturer') {
        $stmt = $pdo->prepare("SELECT dr.*, u.name as unit_name, s.name as student_name FROM drop_requests dr 
                               JOIN units u ON dr.unit_id=u.id
                               JOIN users s ON dr.user_id=s.id
                               WHERE u.lecturer_id=? ORDER BY dr.created_at DESC");
        $stmt->execute([$user_id]);
    } else { // admin
        $stmt = $pdo->query("SELECT dr.*, u.name as unit_name, s.name as student_name FROM drop_requests dr 
                             JOIN units u ON dr.unit_id=u.id
                             JOIN users s ON dr.user_id=s.id
                             ORDER BY dr.created_at DESC");
    }
    return $stmt->fetchAll();
}
