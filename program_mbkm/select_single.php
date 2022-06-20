<?php
include "../connection.php";
include "../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods", "GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// $item_id = isset($_GET['id']) ? $_GET['id'] : die();

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$item_id = $decoded_request['id'];

$query = "SELECT * FROM MBKM_REQUIREMENT WHERE ID = $item_id";
$parse_sql = oci_parse($conn, $query);

oci_execute($parse_sql) or die(oci_error());
$query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);

if ($query_result) {
    createSuccessResponse($query_result, 'Get item success!');
} else {
    createErrorResponse('Get item failed!');
}
?>