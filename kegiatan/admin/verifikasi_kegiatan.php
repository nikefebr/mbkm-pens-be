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
$logbook = $decoded_request['logbook'];
$linkLogbook = $decoded_request['linkLogbook'];

$query = 
    "UPDATE MBKM_REGISTRATION 
    SET 
    DATE_START = '$dateStart',
    DATE_END = '$dateEnd',
    STATUS_KEGIATAN = '$statusKegiatan',
    LOGBOOK = '$logbook',
    LINK_LOGBOOK = '$linkLogbook'
    WHERE ID = '$id'";

$parse_sql = oci_parse($conn, $query);

try {
    oci_execute($parse_sql);
} catch (\Throwable $th) {
    createErrorResponse('Item update failed!');
} finally {
    $statusDokumen = "Belum Diinputkan";
    $reason = "";
    $suggestion = "";
    $document = "";
    $documentName = "";
    $query_dokumen = "INSERT INTO PENGAKUAN_SKS
    VALUES (SEQ_PENGAKUAN_SKS.NEXTVAL, $id, '$statusDokumen',
    '$reason', '$suggestion', '$document', '$documentName')";
    $parse_dokumen = oci_parse($conn, $query_dokumen);
    $execute_dokumen = oci_execute($parse_dokumen) or die(oci_error());

    $statusCertificate = "Belum Diinputkan";
    $certificate = "";
    $certificateName = "";
    $query_certificate = "INSERT INTO PENGUMPULAN_SERTIFIKAT
    VALUES (SEQ_PENGUMPULAN_SERTIFIKAT.NEXTVAL, $id, '$certificate', '$certificateName', '$statusCertificate')";
    $parse_certificate = oci_parse($conn, $query_certificate);
    $execute_certificate = oci_execute($parse_certificate) or die(oci_error());

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