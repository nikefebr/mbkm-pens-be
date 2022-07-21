<?php
    include "../../connection.php";
    include "../../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    $kaprodiId = $input["kaprodiId"];

    $conn = createDatabaseConnection();
    $query = 
        "SELECT P.ID, P.PROGRAM_NAME, P.TAHUN_AJARAN, P.SEMESTER, R.ID AS MBKM_REGISTRATION_ID, R.STUDENT_ID, R.MBKM_PROGRAM_ID,
        R.HANDPHONE, R.DESCRIPTION, R.MITRA_NAME, R.MITRA_ADDRESS, R.LINK_WEBSITE_MITRA,
        R.STATUS, R.KAPRODI_ID, R.DPK_ID, R.REASON, R.SUGGESTION, R.DATE_START,
        R.DATE_END, R.STATUS_KEGIATAN, R.LOGBOOK, R.NAMA_KEGIATAN,
        M.NRP, M.NAMA,
        S.ID AS PENGAKUAN_SKS_ID, S.STATUS_DOKUMEN
        
        FROM MBKM_REGISTRATION R 
            RIGHT JOIN MBKM_PROGRAM P ON P.ID = R.MBKM_PROGRAM_ID
            LEFT JOIN MAHASISWA M ON R.STUDENT_ID = M.NRP
            RIGHT JOIN PENGAKUAN_SKS S ON R.ID = S.MBKM_REGISTRATION_ID
        WHERE R.KAPRODI_ID = $kaprodiId AND R.STATUS_KEGIATAN='Kegiatan Aktif' AND S.STATUS_DOKUMEN != 'Belum Diinputkan' 
        ORDER BY R.ID";

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