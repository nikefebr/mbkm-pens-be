<?php
include "../../connection.php";
include "../../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$item_id = $decoded_request['id'];

$query = "DELETE DOCUMENT_KAPRODI WHERE ID = $item_id";
$parse_sql = oci_parse($conn, $query);
$execute = oci_execute($parse_sql) or die(oci_error());

if ($execute) {
    createSuccessResponse($execute, 'Item deleted successfully!!');
} else {
    createErrorResponse('Delete program failed!');
}
?>