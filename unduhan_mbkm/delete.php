<?php
include "../connection.php";
include "../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$item_id = $decoded_request['id'];
$mbkm_unduhan_id = $item_id;

$query_child = "DELETE MBKM_PROGRAM_UNDUHAN WHERE MBKM_UNDUHAN_ID = $item_id";

$parse_sql = oci_parse($conn, $query_child);

$execute = oci_execute($parse_sql) or die(oci_error());

if ($execute) {
    $query = "DELETE MBKM_UNDUHAN WHERE ID = $item_id";
    $parse_sql = oci_parse($conn, $query);
    $execute = oci_execute($parse_sql) or die(oci_error());
    
    if ($execute)
        createSuccessResponse($execute, 'Item deleted successfully!!');
    else
        createErrorResponse('Delete data failed!');
} else {
    createErrorResponse('Delete program failed!');
}
?>