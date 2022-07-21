<?php
    include "../../connection.php";
    include "../../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    $registrationId = $input["registrationId"];

    $conn = createDatabaseConnection();
    
    $query = 
        "SELECT K.ID, K.MBKM_REGISTRATION_ID, K.MBKM_COURSE_ID, M.KODE, M.MATAKULIAH, M.SKS, K.NILAI 
        FROM MATAKULIAH_KONVERSI K
            RIGHT JOIN MATAKULIAH M ON M.NOMOR = K.MBKM_COURSE_ID
        WHERE MBKM_REGISTRATION_ID = $registrationId";

    $parse_sql = oci_parse($conn, $query);
    $query_result = [];

    oci_execute($parse_sql) or die(oci_error());
    oci_fetch_all($parse_sql, $query_result, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);
        
    if ($query_result) {
        createSuccessResponse($query_result, 'Get list success!');
    } else {
        createErrorResponse('Get item failed!');
    }
?>