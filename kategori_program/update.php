<?php
include "../connection.php";
include "../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods", "PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$id = $decoded_request['id'];
$programName = $decoded_request["programName"];
$description = $decoded_request["description"];

$query = 
    "UPDATE MBKM_KATEGORI_PROGRAM 
    SET 
    KATEGORI_NAME = '$programName',
    DESCRIPTION = '$description' 
    WHERE ID = '$id'";

$parse_sql = oci_parse($conn, $query);

try {
    oci_execute($parse_sql);
} catch (\Throwable $th) {
    createErrorResponse('Item update failed!');
} finally {
    $query = "SELECT * FROM MBKM_KATEGORI_PROGRAM WHERE ID = $id";
    $parse_sql = oci_parse($conn, $query);

    oci_execute($parse_sql) or die(oci_error());
    $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);

    createSuccessResponse($query_result, 'Item updated successfully!');
}
?>