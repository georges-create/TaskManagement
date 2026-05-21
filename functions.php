<?php
// functions.php

/**
 * Send a notification to a user
 *
 * @param PDO $pdo
 * @param int $user_id
 * @param string $role
 * @param string $message
 * @return void
 */
function sendNotification($pdo, $user_id, $role, $message)
{
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, role, message)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $role, $message]);
}
