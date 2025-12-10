<?php
    require_once '../../config.php';
    admin_token_check();
    header('Content-Type: application/json');

    // Get JSON input from fetch()
    $input = json_decode(file_get_contents("php://input"), true);
    $user_id = intval($input['user_id'] ?? 0);
    $action = trim($input['action'] ?? '');

    // Define valid status actions
    $valid_actions = [
        'verify'    => 'verified',
        'demote'    => 'normal',
        'ban'       => 'banned',
        'activate'  => 'normal',
        'reject'    => 'normal'
    ];

    if (!$user_id || !isset($valid_actions[$action])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user or action']);
        exit;
    }

    $new_status = $valid_actions[$action];

    try {
        $stmt = $pdo->prepare("UPDATE users SET profile_status = :status WHERE id = :id");
        $stmt->execute([
            ':status' => $new_status,
            ':id' => $user_id
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => "User status updated to {$new_status}"
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
?>
