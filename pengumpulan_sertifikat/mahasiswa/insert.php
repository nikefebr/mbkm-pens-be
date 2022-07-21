<?php
    include "../../connection.php";
    include "../../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    $id = $input["id"];
    $certificate = $input['certificate'];
    $certificateName = $input['certificateName'];
    $statusDokumen = "Sudah Diinputkan";

    $conn = createDatabaseConnection();

    $sql = "UPDATE PENGUMPULAN_SERTIFIKAT
    SET 
    CERTIFICATE = '$certificate',
    CERTIFICATE_NAME = '$certificateName',
    STATUS_CERTIFICATE = '$statusDokumen'
    WHERE ID = $id";

    $parse = oci_parse($conn, $sql);
    $execute = oci_execute($parse) or die(oci_error());

    if ($execute) {
        $query = "SELECT * FROM PENGUMPULAN_SERTIFIKAT WHERE ID = $id";
        $parse_sql = oci_parse($conn, $query);
    
        $execute = oci_execute($parse_sql) or die(oci_error());

        $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);
        createSuccessResponse($query_result, 'Item added successfully!');
    } else {
        createErrorResponse('Failed add item');
    }
?>