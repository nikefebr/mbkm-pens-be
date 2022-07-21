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
    $search = $input["search"];

    $conn = createDatabaseConnection();

    if ($search == null) {
        $query = 
        "SELECT P.ID, P.PROGRAM_NAME, P.TAHUN_AJARAN, P.SEMESTER, R.ID, R.STUDENT_ID, R.MBKM_PROGRAM_ID,
        R.HANDPHONE, R.DESCRIPTION, R.MITRA_NAME, R.MITRA_ADDRESS, R.LINK_WEBSITE_MITRA,
        R.STATUS, R.KAPRODI_ID, R.DATE_START, R. DATE_END, R.STATUS_KEGIATAN, R.LINK_KEGIATAN, R.DOSEN_WALI_ID,
        R.NAMA_KEGIATAN, M.NRP, M.NAMA
        FROM MBKM_PROGRAM P 
            RIGHT JOIN MBKM_REGISTRATION R ON P.ID = MBKM_PROGRAM_ID 
            LEFT JOIN MAHASISWA M ON R.STUDENT_ID = M.NRP
        WHERE R.KAPRODI_ID = $kaprodiId AND R.STATUS_KEGIATAN='Kegiatan Disetujui' 
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
    } else {
        $query = 
        "SELECT P.ID, P.PROGRAM_NAME, P.TAHUN_AJARAN, P.SEMESTER, R.ID, R.STUDENT_ID, R.MBKM_PROGRAM_ID,
        R.HANDPHONE, R.DESCRIPTION, R.MITRA_NAME, R.MITRA_ADDRESS, R.LINK_WEBSITE_MITRA,
        R.STATUS, R.KAPRODI_ID, R.DATE_START, R. DATE_END, R.STATUS_KEGIATAN, R.LINK_KEGIATAN, R.DOSEN_WALI_ID,
        R.NAMA_KEGIATAN, M.NRP, M.NAMA
        FROM MBKM_PROGRAM P 
            RIGHT JOIN MBKM_REGISTRATION R ON P.ID = MBKM_PROGRAM_ID 
            LEFT JOIN MAHASISWA M ON R.STUDENT_ID = M.NRP
        WHERE (R.KAPRODI_ID = $kaprodiId AND R.STATUS_KEGIATAN='Kegiatan Disetujui') 
        AND (R.NAMA_KEGIATAN LIKE '%$search%' OR P.PROGRAM_NAME LIKE '%$search%' OR R.MITRA_NAME LIKE '%$search%' OR 
        M.NRP LIKE '%$search%' OR M.NAMA LIKE '%$search%' OR P.TAHUN_AJARAN LIKE '%$search%' OR P.SEMESTER LIKE '%$search%' OR 
        R.STATUS LIKE '%$search%')
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
    }
?>