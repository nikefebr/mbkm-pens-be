<?php
include "../connection.php";
include "../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$item_id = $decoded_request['id'];
$mbkm_program_id = $item_id;

$query = "DELETE MBKM_PROGRAM WHERE ID = $item_id";
$parse_sql = oci_parse($conn, $query);

$execute = oci_execute($parse_sql) or die(oci_error());

if ($execute) {
    $query_sql = "SELECT COUNT(ID) FROM MBKM_REQUIREMENT WHERE ID";
    for($x=0; $x<$query_sql; $x++) {
        $sql = "DELETE MBKM_REQUIREMENT WHERE ID = $mbkm_program_id";

        $parse = oci_parse($conn, $sql);
        
        $execute = oci_execute($parse) or die(oci_error());
    }
    createSuccessResponse($execute, 'Item deleted successfully!!');
} else {
    createErrorResponse('Delete item failed!');
}
?>