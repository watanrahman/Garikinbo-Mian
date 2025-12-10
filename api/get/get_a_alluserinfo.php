<?php
    require_once '../../config.php';
    admin_token_check();
    header('Content-Type: application/json');

    try {
        // Create a base SQL query
        $sql = "SELECT id, full_name, profile_pic_url, user_name, email, phone_number, location, created_at, profile_status 
                FROM users WHERE 1=1";
        $params = [];

        // Search filter
        if (!empty($_GET['search'])) {
            $search = "%" . $_GET['search'] . "%";
            $sql .= " AND (id LIKE ? OR full_name LIKE ? OR user_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $params = array_merge($params, [$search, $search, $search, $search, $search]);
        }

        // Status filter
        if (!empty($_GET['status']) && in_array($_GET['status'], ['normal', 'verified', 'review' ,'banned'])) {
            $sql .= " AND profile_status = ?";
            $params[] = $_GET['status'];
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['users' => $users]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
