<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $conn = createDatabaseConnection();
    
    $request = file_get_contents("php://input");
    $decoded_request = json_decode($request, true);

    $kaprodiId = $decoded_request['kaprodiId'];

    $query = 
    "SELECT P.PROGRAM_NAME, P.TAHUN_AJARAN, P.SEMESTER, COUNT(*) AS TOTAL 
    FROM MBKM_REGISTRATION R
    LEFT JOIN MBKM_PROGRAM P ON P.ID = R.MBKM_PROGRAM_ID
    WHERE R.STATUS_KEGIATAN='Kegiatan Aktif' AND R.KAPRODI_ID = $kaprodiId 
    GROUP BY R.MBKM_PROGRAM_ID, P.PROGRAM_NAME, P.TAHUN_AJARAN, P.SEMESTER
    ORDER BY P.TAHUN_AJARAN";

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