<?php
function getGermanMonths(): array {
    return [
        1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
        5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'
    ];
}

function getGermanWeekdays(): array {
    return [
        'Monday' => 'Montag',
        'Tuesday' => 'Dienstag',
        'Wednesday' => 'Mittwoch',
        'Thursday' => 'Donnerstag',
        'Friday' => 'Freitag',
        'Saturday' => 'Samstag',
        'Sunday' => 'Sonntag'
    ];
}

function getUpcomingEvents(PDO $db, int $user_id): array {
    $query = "SELECT e.*, 
          (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_count,
          (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id AND r.user_id = :user_id) as user_registered,
          u.username as organizer_name
          FROM events e
          JOIN users u ON e.organizer_id = u.id
          WHERE e.date > NOW()
          ORDER BY e.date ASC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function groupEventsByYearAndMonth(array $events): array {
    $events_by_month = [];

    foreach ($events as $event) {
        $month = date('n', strtotime($event['date']));
        $year = date('Y', strtotime($event['date']));

        if (!isset($events_by_month[$year][$month])) {
            $events_by_month[$year][$month] = [];
        }

        $events_by_month[$year][$month][] = $event;
    }

    return $events_by_month;
}
?>
