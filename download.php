<?php
if (isset($_GET['file'])) {
    $file_name = $_GET['file'];

    // Define the directory where your files are stored
    $file_dir = 'uploads/' . $file_name; // Modify the path based on your setup

    // Check if the file exists
    if (file_exists($file_dir)) {
        // Set headers to indicate it's a file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_dir) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_dir));

        // Read and output the file
        readfile($file_dir);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    // Handle the case where the 'file' parameter is not set
    echo "Invalid request.";
}
?>