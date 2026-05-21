<?php
function getMyEventsForUser(PDO $db, int $user_id): array {
    $query = "SELECT r.id as registration_id, r.registered_at, e.*, 
              (SELECT COUNT(*) FROM registrations r2 WHERE r2.event_id = e.id) as registered_count,
              u.username as organizer_name
              FROM registrations r
              JOIN events e ON r.event_id = e.id
              JOIN users u ON e.organizer_id = u.id
              WHERE r.user_id = :user_id
              ORDER BY e.date ASC";

    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getGermanWeekdays(): array {
    return [
        'Monday' => 'Montag',
        'Tuesday' => 'Dienstag',
        'Wednesday' => 'Mittwoch',
        'Thursday' => 'Donnerstag',
        'Friday' => 'Freitag',
        'Saturday' => 'Samstag',
        'Sunday' => 'Sonntag',
    ];
}
