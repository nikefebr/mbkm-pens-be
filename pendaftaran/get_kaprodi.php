<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    $kelas = $input["kelas"];

    $conn = createDatabaseConnection();
    
    $query = 
        "SELECT K.NOMOR, K.PROGRAM, K.JURUSAN, K.KELAS, 
        P.NOMOR, P.PROGRAM, P.JURUSAN, P.KEPALA, P.NAMA_BANPT
        
        FROM KELAS K 
            RIGHT JOIN PROGRAM_STUDI P ON K.JURUSAN = P.JURUSAN AND K.PROGRAM = P.PROGRAM
        
        WHERE K.NOMOR = $kelas ORDER BY K.NOMOR";

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