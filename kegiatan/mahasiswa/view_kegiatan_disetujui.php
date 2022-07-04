<?php
    include "../../connection.php";
    include "../../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");

    $conn = createDatabaseConnection();
    $query = "SELECT P.ID, P.PROGRAM_NAME, R.ID, R.SEMESTER_ID, R.STUDENT_ID, R.MBKM_PROGRAM_ID,
    R.HANDPHONE, R.DESCRIPTION, R.MITRA_NAME, R.MITRA_ADDRESS, R.LINK_WEBSITE_MITRA,
    R.STATUS, R.PRODI_ID, R.COURSE_ID, R.DPK_ID, R.REASON, R.SUGGESTION, R.DATE_START,
    R. DATE_END, R.STATUS_KEGIATAN, R.LOGBOOK
    FROM MBKM_PROGRAM P RIGHT JOIN MBKM_REGISTRATION R ON P.ID = MBKM_PROGRAM_ID 
    WHERE R.STATUS_KEGIATAN='Kegiatan Disetujui' ORDER BY R.ID";

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