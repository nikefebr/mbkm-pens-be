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
$name = $decoded_request["name"];
$description = $decoded_request["description"];
$document = $decoded_request["document"];
$documentName = $decoded_request["documentName"];

$query = 
    "UPDATE DOCUMENT_KAPRODI 
    SET 
    NAME = '$name',
    DESCRIPTION = '$description', 
    DOCUMENT = '$document',
    DOCUMENT_NAME = '$documentName'
    WHERE ID = '$item_id'";

$parse_sql = oci_parse($conn, $query);

try {
    oci_execute($parse_sql);
} catch (\Throwable $th) {
    createErrorResponse('Item update failed!');
} finally {
    $query = "SELECT * FROM DOCUMENT_KAPRODI WHERE ID = $item_id";
    $parse_sql = oci_parse($conn, $query);

    oci_execute($parse_sql) or die(oci_error());
    $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);

    createSuccessResponse($query_result, 'Item updated successfully!');
}
?>