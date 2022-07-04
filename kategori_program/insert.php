<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);
 
    $programName = $input["programName"];
    $description = $input["description"];
    $linkWebsite = $input["linkWebsite"];
    $deadline = $input["deadline"];
    $limit = $input["limit"];
    $requirement = $input["requirement"];

    $query = "INSERT INTO MBKM_KATEGORI_PROGRAM VALUES ('$programName', '$description', SEQ_KATEGORI_PROGRAM.NEXTVAL, '$linkWebsite', '$deadline', '$limit') returning ID into :inserted_id";
    
    $conn = createDatabaseConnection();

    $parse_sql = oci_parse($conn, $query);

    oci_bind_by_name($parse_sql, ":inserted_id", $idNumber);

    $exe = oci_execute($parse_sql) or die(oci_error());

    if ($exe) {
        for($x=0; $x<count($requirement); $x++) {
            $sql = "INSERT INTO MBKM_REQUIREMENT VALUES (SEQ_REQUIREMENT.NEXTVAL, $idNumber, '$requirement[$x]')";

            $parse = oci_parse($conn, $sql);
            
            $execute = oci_execute($parse) or die(oci_error());
        }
        
        $query = "SELECT * FROM MBKM_KATEGORI_PROGRAM WHERE ID = $idNumber";
        $parse_sql = oci_parse($conn, $query);
    
        oci_execute($parse_sql) or die(oci_error());
        $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);
  
        createSuccessResponse($query_result, 'Item added successfully!');
    }
?>