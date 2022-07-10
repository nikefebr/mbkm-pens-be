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
$dateStart = $decoded_request['dateStart'];
$dateEnd = $decoded_request['dateEnd'];
$statusKegiatan = "Kegiatan Aktif";
$statusDokumen = "Belum Diinputkan";
$logbook = $decoded_request['logbook'];

$query = 
    "UPDATE MBKM_REGISTRATION 
    SET 
    DATE_START = '$dateStart',
    DATE_END = '$dateEnd',
    STATUS_KEGIATAN = '$statusKegiatan',
    LOGBOOK = '$logbook',
    STATUS_DOKUMEN = '$statusDokumen'
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
    $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);

    if($execute) {
        $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);
        createSuccessResponse($query_result, 'Item updated successfully!');
    } else {
        createErrorResponse('Failed add item');
    }
}
?>