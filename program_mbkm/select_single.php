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

$item_id = $decoded_request['id'];

$query = 
    "SELECT  P.ID, P.PROGRAM_NAME, P.DESCRIPTION, P.SEMESTER, P.TAHUN_AJARAN, P.LIMIT, K.MBKM_KATEGORI_ID
    FROM MBKM_PROGRAM P 
        RIGHT JOIN MBKM_KATEGORI K ON P.ID = K.MBKM_PROGRAM_ID
    WHERE P.ID = $item_id
    ORDER BY P.ID";

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