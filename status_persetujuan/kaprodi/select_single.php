<?php
include "../../connection.php";
include "../../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods", "GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$id = $decoded_request['id'];

$query = 
    "SELECT R.ID, R.STUDENT_ID, R.MBKM_PROGRAM_ID, R.HANDPHONE, R.DESCRIPTION,
    R.MITRA_NAME, R.MITRA_ADDRESS, R.LINK_WEBSITE_MITRA, R.STATUS, R.KAPRODI_ID,
    R.DPK_ID, R.REASON, R.SUGGESTION, R.DATE_START, R.DATE_END, R.STATUS_KEGIATAN,
    R.LOGBOOK, R.LINK_KEGIATAN, R.DOSEN_WALI_ID, R.NAMA_KEGIATAN,
    M.ID, M.MBKM_REGISTRATION_ID, M.MBKM_COURSE_ID
    FROM MBKM_REGISTRATION R
        LEFT JOIN MATAKULIAH_KONVERSI M ON R.ID = M.MBKM_REGISTRATION_ID 
    WHERE R.ID = $id";

$parse = oci_parse($conn, $query);
oci_execute($parse) or die(oci_error());
$query_result = [];
oci_fetch_all($parse, $query_result, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);

if ($query_result) {
    createSuccessResponse($query_result, 'Get item success!');
} else {
    createErrorResponse('Get item failed!');
}
?>