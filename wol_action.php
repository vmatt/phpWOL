<?php
include_once ('wol_functions.php');

$data = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');

$WOLresponse = new stdClass();

if (isset($data['hostName']) && isset($data['ipAddress']) && isset($data['macAddress'])) {
    $msg = 'Success';
    wakeUp($data['macAddress'], $data['ipAddress'], $msg);
    if ($msg != 'Success') {
        http_response_code(500);
        $WOLresponse->status = false;
        $WOLresponse->error = $msg;
    } else {
        http_response_code(200);
        $WOLresponse->status = "OK";
    }
    echo json_encode($WOLresponse);
} else {
    $WOLresponse = new stdClass();
    $WOLresponse->status = false;
    $WOLresponse->error = 'Invalid request.';
    http_response_code(400);
    echo json_encode($WOLresponse);
}

?>