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
    "SELECT  K.PROGRAM_NAME, K.DESCRIPTION, K.ID, K.LINK_WEBSITE, K.DEADLINE, R.REQUIREMENT 
    FROM MBKM_KATEGORI_PROGRAM K RIGHT JOIN MBKM_REQUIREMENT R ON K.ID = R.MBKM_KATEGORI_ID
    WHERE K.ID = $item_id";

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