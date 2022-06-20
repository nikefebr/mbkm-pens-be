<?php
	function createSuccessResponse($data, $message) {
        $response = array(
            'success' => true,
            'message' => $message,
            'data' => $data
        );

        echo json_encode($response);
	}
	function createErrorResponse($message) {
        $response = array(
            'success' => false,
            'message' => $message,
            'data' => null
        );

        echo json_encode($response);
	}
?>