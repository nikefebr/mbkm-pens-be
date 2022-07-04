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
$status = $decoded_request['status'];
$statusKegiatan = 'Kegiatan Terdaftar';
$mataKuliahKonversiId = $decoded_request['mataKuliahKonversiId'];
$dosenPembimbingKegiatanId = $decoded_request['dosenPembimbingKegiatanId'];
$documents = $decoded_request['documents'];
$documentsName = $decoded_request['documentsName'];

$query = 
    "UPDATE MBKM_REGISTRATION 
    SET 
    STATUS = '$status', 
    COURSE_ID = $mataKuliahKonversiId,
    DPK_ID = $dosenPembimbingKegiatanId,
    STATUS_KEGIATAN = '$statusKegiatan'
    WHERE ID = '$id'";

$parse_sql = oci_parse($conn, $query);

try {
    oci_execute($parse_sql);
} catch (\Throwable $th) {
    createErrorResponse('Item update failed!');
} finally {
    for($x=0; $x<count($documents); $x++) {
        for($y=0; $y<count($documentsName); $y++) {
            $sql = "INSERT INTO APPROVAL_DOCUMENT VALUES (SEQ_APPROVAL_DOCUMENT.NEXTVAL, $id, '$documents[$x]', '$documentsName[$y]')";

            $parse = oci_parse($conn, $sql);
            
            $execute = oci_execute($parse) or die(oci_error());
        }
    }

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