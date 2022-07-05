<?php
include "../connection.php";
include "../response.php";

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
    "SELECT R.ID, R.MBKM_PROGRAM_ID,R.HANDPHONE, R.DESCRIPTION, R.MITRA_NAME,
    R.MITRA_ADDRESS, R.LINK_WEBSITE_MITRA, R.STATUS, R.COURSE_ID,
    R.DPK_ID, R.REASON, R.SUGGESTION, R.DATE_START, R.DATE_END, R.LINK_KEGIATAN,
    R.LINK_WEBSITE_PROGRAM, D.REGISTRATION_ID, D.DOCUMENT, D.DOCUMENT_NAME,
    P.ID, P.PROGRAM_NAME
    FROM MBKM_REGISTRATION R 
        RIGHT JOIN REGISTRATION_DOCUMENT D ON R.ID = D.REGISTRATION_ID 
        RIGHT JOIN MBKM_PROGRAM P ON P.ID = R.MBKM_PROGRAM_ID
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