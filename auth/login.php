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
        $conn = createDatabaseConnection();
        
        if(array_key_exists("NIP", $hasil_decoded)) {
            $nip = $hasil_decoded['NIP'];

            if($nip == 199210122018032001) {
                $role = 'Admin';
                $query = "SELECT * FROM PEGAWAI WHERE NIP = '$nip'";
                $parse_sql = oci_parse($conn, $query);
                
                oci_execute($parse_sql) or die(oci_error());
                $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);

                $appendDetail = (array)$query_result;
                $appendDetail['NETID'] = $hasil_decoded['netid'];
                $appendDetail['role'] = $role;
                $appendDetail = (object)$appendDetail;

                createSuccessResponse($appendDetail, 'Login success!');
            } else {
                $query_pegawai = "SELECT * FROM PEGAWAI WHERE NIP = '$nip'";
                $parse_pegawai = oci_parse($conn, $query_pegawai);
                oci_execute($parse_pegawai) or die(oci_error());
                $pegawai = oci_fetch_object($parse_pegawai);
                $idPegawai = $pegawai->NOMOR;

                $query_dosen_wali = "SELECT DOSEN_WALI FROM MAHASISWA WHERE DOSEN_WALI=$idPegawai";
                $parse_dosen_wali = oci_parse($conn, $query_dosen_wali);
                oci_execute($parse_dosen_wali) or die(ocierror());
                $dosen_wali = oci_fetch_object($parse_dosen_wali);

                $query_kaprodi = "SELECT KEPALA FROM PROGRAM_STUDI WHERE KEPALA=$idPegawai";
                $parse_kaprodi = oci_parse($conn, $query_kaprodi);
                oci_execute($parse_kaprodi) or die(ocierror());
                $kaprodi = oci_fetch_object($parse_kaprodi);

                if($kaprodi != false){
                    $role = 'Kaprodi';

                    $appendDetail = (array)$pegawai;
                    $appendDetail['NETID'] = $hasil_decoded['netid'];
                    $appendDetail['role'] = $role;
                    $appendDetail = (object)$appendDetail;

                    createSuccessResponse($appendDetail, 'Login success!');
                } else if($dosen_wali != false){
                    $role = 'DPA';

                    $appendDetail = (array)$pegawai;
                    $appendDetail['NETID'] = $hasil_decoded['netid'];
                    $appendDetail['role'] = $role;
                    $appendDetail = (object)$appendDetail;

                    createSuccessResponse($appendDetail, 'Login success!');
                } else {
                    createErrorResponse('Login failed!');
                }
            }
        }
        else {
            $nrp = $hasil_decoded['NRP'];

            if($nrp) {
                $role = 'Mahasiswa';
                $query = "SELECT * FROM MAHASISWA WHERE NRP = '$nrp'";
                $parse_sql = oci_parse($conn, $query);
                
                oci_execute($parse_sql) or die(oci_error());
                $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);
    
                $appendDetail = (array)$query_result;
                $appendDetail['NETID'] = $hasil_decoded['netid'];
                $appendDetail['role'] = $role;
                $appendDetail = (object)$appendDetail;
    
                createSuccessResponse($appendDetail, 'Login success!');
            }
        }

    } else {
        createErrorResponse('Login failed!');
    }
?>