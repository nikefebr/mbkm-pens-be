<?php
    include "../../connection.php";
    include "../../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $conn = createDatabaseConnection();

    $input = json_decode(file_get_contents("php://input"), true);

    $dosenWaliId = $input["dosenWaliId"];

    $query = 
        "SELECT MITRA_NAME
        FROM MBKM_REGISTRATION
        WHERE DOSEN_WALI_ID = $dosenWaliId AND STATUS='Belum Disetujui' 
            OR DOSEN_WALI_ID = $dosenWaliId AND STATUS='Disetujui DPA' 
            OR DOSEN_WALI_ID = $dosenWaliId AND STATUS='Ditolak'
        GROUP BY MITRA_NAME";

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