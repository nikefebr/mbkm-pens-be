<?php
    include "../../connection.php";
    include "../../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    $id = $input["id"];
    $documents = $input['documents'];
    $documentsName = $input['documentsName'];

    $conn = createDatabaseConnection();

    for($y=0; $y<count($documentsName); $y++) {
        $sql = "INSERT INTO DOCUMENT_PENGAKUAN_SKS 
        VALUES (SEQ_DOCUMENT_PENGAKUAN_SKS.NEXTVAL, $id, '$documents[$y]', '$documentsName[$y]')";
        $parse = oci_parse($conn, $sql);
        $execute = oci_execute($parse) or die(oci_error());

        if ($execute) {
            $statusDokumen = "Belum Disetujui";
    
            $query_status = 
            "UPDATE PENGAKUAN_SKS
            SET STATUS_DOKUMEN = '$statusDokumen'
            WHERE ID = $id";
    
            $parse_status = oci_parse($conn, $query_status);
            $execute_status = oci_execute($parse_status) or die(oci_error());
    
            $query = "SELECT * FROM PENGAKUAN_SKS WHERE ID = $id";
            $parse_sql = oci_parse($conn, $query);
        
            $execute = oci_execute($parse_sql) or die(oci_error());
    
            if($execute) {
                $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);
                createSuccessResponse($query_result, 'Item added successfully!');
            } else {
                createErrorResponse('Failed add item');
            }
        }
    }
?>