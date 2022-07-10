<?php
    include "../../connection.php";
    include "../../response.php";

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
        M.NOMOR, M.MATAKULIAH, M.KODE, M.SKS, M.PROGRAM, M.JURUSAN, M.KELAS 
        FROM KELAS K
            RIGHT JOIN MATAKULIAH M ON K.PROGRAM = M.PROGRAM AND K.JURUSAN = M.JURUSAN
            AND K.KELAS = M.KELAS
        WHERE K.NOMOR = $kelas";

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