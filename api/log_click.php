<?php 
header('Content-Type: application/json');
include('../admin/PM/PM.php'); 

try {

    $readCon = PM::getSingleton("Database")->getReadCon();
    $writeCon = PM::getSingleton("Database")->getWriteCon();

    $data = json_decode(file_get_contents('php://input'), true);
    $bannerId = $data['banner_id'];
    $csrfToken = $data['csrf_token'] ?? '';

    if (!PM::getSingleton("Common")->verifyCsrfToken($csrfToken)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'CSRF verify fail'
        ]);
        exit;
    }

    if (!$bannerId || $bannerId <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Incorrect banner_id'
        ]);
        exit;
    }

    $sql = "UPDATE banner SET click = click + 1 WHERE id = :id";
    $stmt = $writeCon->prepare($sql);
    $stmt->execute(['id' => $bannerId]);
    $rowCount = $stmt->rowCount();

    if ($rowCount === 0) {
        $checkStmt = $readCon->prepare("SELECT COUNT(*) FROM banner WHERE id = :id");
        $checkStmt->execute(['id' => $bannerId]);
        if ($checkStmt->fetchColumn() == 0) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner not exist'
            ]);
            exit;
        }
    }

    $clientIp = PM::getSingleton("Common")->getClientIp();

    $sqlData = array(
        "banner_id" => $bannerId,
        "ip_address" => $clientIp,
        "created_at" => date("Y-m-d H:i:s"),
    );

    $id = PM::getSingleton("Database")->insertRow("banner_click_log", $sqlData);

    if ($id === false) {
        throw new Exception('Log fail');
    }

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Click log success',
        'log_id' => $id
    ]);

} catch (Exception $e) {
    error_log('log_click.php error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server Fail'
    ]);
}

unset($stmt, $checkStmt, $readCon, $writeCon);