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
        "SELECT P.ID, P.PROGRAM_NAME, P.TAHUN_AJARAN, P.SEMESTER, R.ID, R.STUDENT_ID, R.MBKM_PROGRAM_ID,
        R.HANDPHONE, R.DESCRIPTION, R.MITRA_NAME, R.MITRA_ADDRESS, R.LINK_WEBSITE_MITRA,
        R.STATUS, R.KAPRODI_ID, R.DATE_START, R. DATE_END, R.LINK_KEGIATAN, R.DOSEN_WALI_ID,
        R.NAMA_KEGIATAN, M.NRP, M.NAMA
        FROM MBKM_PROGRAM P 
            RIGHT JOIN MBKM_REGISTRATION R ON P.ID = MBKM_PROGRAM_ID 
            LEFT JOIN MAHASISWA M ON R.STUDENT_ID = M.NRP
        WHERE R.KAPRODI_ID = $kaprodiId AND R.STATUS='Disetujui DPA' 
        OR R.KAPRODI_ID = $kaprodiId AND R.STATUS='Disetujui Kaprodi' 
        OR R.KAPRODI_ID = $kaprodiId AND R.STATUS='Ditolak' 
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