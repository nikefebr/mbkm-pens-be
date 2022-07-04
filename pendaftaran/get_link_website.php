<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    $mbkmKategoriId = $input["mbkmKategoriId"];

    $conn = createDatabaseConnection();
    
    $query = 
        "SELECT K.ID, K.LINK_WEBSITE, P.MBKM_KATEGORI_ID
        
        FROM MBKM_KATEGORI_PROGRAM K
            RIGHT JOIN MBKM_PROGRAM P ON K.ID = P.MBKM_KATEGORI_ID
        
        WHERE P.MBKM_KATEGORI_ID = $mbkmKategoriId";

    $parse_sql = oci_parse($conn, $query);
    $query_result = [];

    oci_execute($parse_sql) or die(oci_error());
    oci_fetch_all($parse_sql, $query_result, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);
        
    if ($query_result) {
        createSuccessResponse($query_result, 'Get list success!');
    } else {
        createErrorResponse('Get item failed!');
    }
?>