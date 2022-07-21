<?php
include "../../connection.php";
include "../../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods", "PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$id = $decoded_request['id'];
$picMitra = $decoded_request['picMitra'];
$kontakPicMitra = $decoded_request['kontakPicMitra'];

$query = 
    "UPDATE MBKM_REGISTRATION 
    SET 
    PIC_MITRA = '$picMitra',
    KONTAK_PIC_MITRA = $kontakPicMitra
    WHERE ID = '$id'";

$parse_sql = oci_parse($conn, $query);

try {
    oci_execute($parse_sql);
} catch (\Throwable $th) {
    createErrorResponse('Item update failed!');
} finally {
    $query = "SELECT * FROM MBKM_REGISTRATION WHERE ID = $id";
    $parse_sql = oci_parse($conn, $query);

    $execute = oci_execute($parse_sql) or die(oci_error());

    if($execute) {
        $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);
        createSuccessResponse($query_result, 'Item updated successfully!');
    } else {
        createErrorResponse('Failed add item');
    }
}
?>