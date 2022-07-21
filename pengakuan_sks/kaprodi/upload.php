<?php
    include "../../connection.php";
    include "../../response.php";

    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Origin: *");

    $folderPengakuanSks = '/var/www/html/mis110/contents/uploads/pengakuan_sks/';
    $document = 'contents/uploads/pengakuan_sks/' . basename($_FILES['file']['name']);
    $documentPengakuanSks = $folderPengakuanSks . basename($_FILES['file']['name']);
    $documentName = $_POST['name'];
    $documentDescription = $_POST['description'];

    if(move_uploaded_file($_FILES['file']['tmp_name'], $documentPengakuanSks)) {
        $file = new stdClass();
        $file->name = $documentName;
        $file->description = $documentDescription;
        $file->uri = $document;
        $query_result = $file;
        createSuccessResponse($query_result, 'Item uploaded successfully!');
    } else {
        createErrorResponse('Upload item failed!');
    }
?>