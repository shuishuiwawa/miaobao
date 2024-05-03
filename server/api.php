<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
// Set the response headers for JSON
header('Content-Type: application/json');

// Global variable to store the key-value pairs
$global_data = array();
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Check if the request method is GET or POST
$method = $_SERVER['REQUEST_METHOD'];

// Initialize the response array
$response = array();

// Handle GET requests (get value)
if ($method === 'GET') {
    // Check if the key parameter is provided
    if (isset($_GET['key'])) {
        $key = $_GET['key'];
        $value = $redis->get($key);

        // Check if the key exists in the global data
        if ($value !== false) {
            $response['value'] = $value;
            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Key not found';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Key parameter is missing';
    }
}

// Handle POST requests (set value)
if ($method === 'POST') {
    // Get the key and value from the request body
    $request_data = json_decode(file_get_contents('php://input'), true);

    // Check if the key and value are provided
    if (isset($request_data['key'])) {
        $key = $request_data['key'];

        $newValue = $redis->incr($key);
        if ($newValue > 22) {
            $redis->set($key, 0);
        }
        // the value of the key will be initialized and incremented
        $response['status'] = 'success';
        $response['message'] = 'Value set successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Key or value parameter is missing';
    }
}

// Return the response as JSON
echo json_encode($response);
?>
