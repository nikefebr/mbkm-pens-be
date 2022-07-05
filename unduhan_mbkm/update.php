<?php
include "../connection.php";
include "../response.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = createDatabaseConnection();

$request = file_get_contents("php://input");
$decoded_request = json_decode($request, true);

$item_id = $decoded_request['id'];
$mbkmKategoriId = $decoded_request["mbkmKategoriId"];
$unduhanName = $decoded_request["unduhanName"];
$description = $decoded_request["description"];
$document = $decoded_request["document"];

$query = 
    "UPDATE MBKM_UNDUHAN 
    SET 
    UNDUHAN_NAME = '$unduhanName',
    DESCRIPTION = '$description', 
    DOCUMENT = '$document'
    WHERE ID = '$item_id'";

$parse_sql = oci_parse($conn, $query);

try {
    oci_execute($parse_sql);

    $sql = "SELECT ID FROM MBKM_PROGRAM_UNDUHAN WHERE MBKM_UNDUHAN_ID = $id";
    $parse = ociparse($conn, $sql);
    $execute = oci_execute($parse) or die (ocierror());
    $query_result = [];
    oci_fetch_all($parse, $query_result, 0, 0, OCI_FETCHSTATEMENT_BY_ROW);

    for($x=0; $x<count($query_result); $x++) {
        $variable = $query_result[$x]['ID'];
        $sql = "UPDATE MBKM_PROGRAM_UNDUHAN SET MBKM_KATEGORI_ID = '$mbkmKategoriId[$x]' 
        WHERE ID = $variable";

        $parse = oci_parse($conn, $sql);
        
        $execute = oci_execute($parse) or die(oci_error());
    }
} catch (\Throwable $th) {
    createErrorResponse('Item update failed!');
} finally {
    $query = "SELECT * FROM MBKM_UNDUHAN WHERE ID = $item_id";
    $parse_sql = oci_parse($conn, $query);

    oci_execute($parse_sql) or die(oci_error());
    $query_result = oci_fetch_object($parse_sql, OCI_ASSOC+OCI_RETURN_NULLS);

    createSuccessResponse($query_result, 'Item updated successfully!');
}
?>