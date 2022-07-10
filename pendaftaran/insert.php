<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    $studentId = $input["studentId"];
    $programId = $input["programId"];
    $handphone = $input["handphone"];
    $description = $input["description"];
    $mitraName = $input["mitraName"];
    $mitraAddress = $input["mitraAddress"];
    $linkWebsiteMitra = $input["linkWebsiteMitra"];
    $status = "Belum Disetujui";
    $kaprodiId = $input["kaprodiId"];
    $dpkId = 0;
    $reason = "";
    $suggestion = "";
    $dateStart = $input["dateStart"];
    $dateEnd = $input["dateEnd"];
    $statusKegiatan = "";
    $logbook = "";
    $linkKegiatan = $input["linkKegiatan"];
    $dosenWaliId = $input["dosenWaliId"];
    $namaKegiatan = $input["namaKegiatan"];
    $statusDokumen = "";
    $documents = $input["documents"];
    $documentsName = $input["documentsName"];

    $query = "INSERT INTO MBKM_REGISTRATION 
    VALUES (SEQ_REGISTRATION.NEXTVAL, $studentId,
    $programId, $handphone, '$description', '$mitraName', '$mitraAddress', '$linkWebsiteMitra',
    '$status', $kaprodiId, $dpkId, '$reason', '$suggestion', '$dateStart', '$dateEnd', 
    '$statusKegiatan', '$logbook', '$linkKegiatan', $dosenWaliId, '$namaKegiatan', '$statusDokumen') 
    returning ID into :inserted_id";
    
    $conn = createDatabaseConnection();

    $parse_sql = oci_parse($conn, $query);

    oci_bind_by_name($parse_sql, ":inserted_id", $idNumber);

    $exe = oci_execute($parse_sql) or die(oci_error());

    if ($exe) {
        for($y=0; $y<count($documentsName); $y++) {
            $sql = "INSERT INTO REGISTRATION_DOCUMENT 
            VALUES (SEQ_REGISTRATION_DOCUMENT.NEXTVAL, $idNumber, '$documents[$y]', '$documentsName[$y]')";

            $parse = oci_parse($conn, $sql);
            
            $execute = oci_execute($parse) or die(oci_error());
        }

        $query = "SELECT * FROM MBKM_REGISTRATION WHERE ID = $idNumber";
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