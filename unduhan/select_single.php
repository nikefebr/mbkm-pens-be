<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $conn = createDatabaseConnection();

    $request = file_get_contents("php://input");
    $decoded_request = json_decode($request, true);

    $kategoriProgramId = $decoded_request['kategoriProgramId'];

    $query = 
        "SELECT U.ID, U.UNDUHAN_NAME, U.DESCRIPTION, U.DOCUMENT,U.DOCUMENT_NAME, P.MBKM_KATEGORI_ID
        FROM MBKM_UNDUHAN U
        RIGHT JOIN MBKM_PROGRAM_UNDUHAN P ON P.MBKM_UNDUHAN_ID = U.ID
        WHERE P.MBKM_KATEGORI_ID = $kategoriProgramId";

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