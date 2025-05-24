<?php 
header('Content-Type: application/json');
include('../admin/PM/PM.php'); 

try {

    $readCon = PM::getSingleton("Database")->getReadCon();
    $writeCon = PM::getSingleton("Database")->getWriteCon();

    $data = json_decode(file_get_contents('php://input'), true);
    $bannerStats = $data['banner_stats'] ?? null;
    $csrfToken = $data['csrf_token'] ?? '';

    if (!PM::getSingleton("Common")->verifyCsrfToken($csrfToken)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'CSRF verify fail : '.$_SESSION['csrf_token']
        ]);
        exit;
    }

    if (!$bannerStats || !is_array($bannerStats) || empty($bannerStats)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Incorrect banner_stats'
        ]);
        exit;
    }

    $bannerIds = [];
    foreach ($bannerStats as $bannerId => $stats) {
        $bannerId = (int)$bannerId;
        $impressions = (int)($stats['impressions'] ?? 0);
        $clicks = (int)($stats['clicks'] ?? 0);

        if ($bannerId <= 0) {
            continue;
        }

        $sql = "UPDATE banner SET impression = impression + :impression, click = click + :click WHERE id = :id";
        $stmt = $writeCon->prepare($sql);
        $stmt->execute([
            'impression' => $impressions,
            'click' => $clicks,
            'id' => $bannerId
        ]);
        $rowCount = $stmt->rowCount();

        if ($rowCount > 0) {
            $bannerIds[] = $bannerId;
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Banner stats log success',
        'banner_ids' => $bannerIds
    ]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server Fail'
    ]);
}

unset($stmt, $checkStmt, $readCon, $writeCon);