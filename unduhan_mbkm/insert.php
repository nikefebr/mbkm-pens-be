<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);
 
    $mbkmKategoriId = $input["mbkmKategoriId"];
    $unduhanName = $input["unduhanName"];
    $description = $input["description"];
    $document = $input["document"];
    $documentName = $input["documentName"];

    $query = "INSERT INTO MBKM_UNDUHAN VALUES (SEQ_UNDUHAN.NEXTVAL, '$unduhanName', '$description', '$document', '$documentName') returning ID into :inserted_id";
    
    $conn = createDatabaseConnection();

    $parse_sql = oci_parse($conn, $query);

    oci_bind_by_name($parse_sql, ":inserted_id", $idNumber);

    $exe = oci_execute($parse_sql) or die(oci_error());

    if ($exe) {
        for($x=0; $x<count($mbkmKategoriId); $x++) {
            $sql = "INSERT INTO MBKM_PROGRAM_UNDUHAN VALUES (SEQ_MBKM_PROGRAM_UNDUHAN.NEXTVAL, $idNumber, '$mbkmKategoriId[$x]')";

            $parse = oci_parse($conn, $sql);
            
            $execute = oci_execute($parse) or die(oci_error());
        }

        $query = "SELECT * FROM MBKM_UNDUHAN WHERE ID = $idNumber";
        $parse_sql = oci_parse($conn, $query);
    
        $execute = oci_execute($parse_sql) or die(oci_error());

        if($execute) {
            $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);
            createSuccessResponse($query_result, 'Item added successfully!');
        } else {
            createErrorResponse('Failed add item');
        }
    }
?>