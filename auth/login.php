<?php
    include "../connection.php";
    include "../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $request = file_get_contents("php://input");
    $decoded_request = json_decode($request, true);

    $u = $decoded_request['email'];
    $p = $decoded_request['password'];

    $header=array("netid: $u","password: ".base64_encode($p));
    $data = curl_init();
    curl_setopt($data, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($data, CURLOPT_HTTPHEADER, $header);
    curl_setopt($data, CURLOPT_URL, "https://login.pens.ac.id/auth/");
    curl_setopt($data, CURLOPT_TIMEOUT, 9);

    $hasil = curl_exec($data);
    curl_close($data);
    
    if ($hasil) {
        $hasil_decoded = json_decode($hasil, true);
        $nrp = $hasil_decoded['NRP'];

        $conn = createDatabaseConnection();
        $query = "SELECT * FROM MAHASISWA WHERE NRP = '$nrp'";
        $parse_sql = oci_parse($conn, $query);
        
        oci_execute($parse_sql) or die(oci_error());
        $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);

        $appendDetail = (array)$query_result;
        $appendDetail['NETID'] = $hasil_decoded['netid'];
        $appendDetail = (object)$appendDetail;

        createSuccessResponse($appendDetail, 'Login success!');
    } else {
        createErrorResponse('Login failed!');
    }
?>