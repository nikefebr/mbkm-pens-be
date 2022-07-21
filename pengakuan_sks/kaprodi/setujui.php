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

$idPengakuanSks = $decoded_request['id'];
$status = "Diterima";
$mataKuliah = $decoded_request["mataKuliah"];
$document = $decoded_request["document"];
$documentName = $decoded_request["documentName"];

for($x=0; $x<count($mataKuliah); $x++) {
    $id = $mataKuliah[$x]['ID'];
    $nilai = $mataKuliah[$x]['nilai'];
    $query = 
    "UPDATE MATAKULIAH_KONVERSI
    SET NILAI = $nilai
    WHERE ID = $id";

    $parse_sql = oci_parse($conn, $query);

    try {
        oci_execute($parse_sql);

        $sql = 
        "UPDATE PENGAKUAN_SKS
        SET 
        STATUS_DOKUMEN = '$status',
        DOCUMENT = '$document',
        DOCUMENT_NAME = '$documentName'
        WHERE ID = $idPengakuanSks";

        $parse = oci_parse($conn, $sql);
        $execute_sql = oci_execute($parse) or die(oci_error());

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
    } catch (\Throwable $th) {
        createErrorResponse('Item update failed!');
    }
}
?>