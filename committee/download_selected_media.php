<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['selected_items'])) {
        // For images
        $selectedItems = json_decode($_POST['selected_items']);
        $mediaType = 'photo';
        $mediaPath = '../uploads/'; // Path to images folder
    } elseif (isset($_POST['selected_videos'])) {
        // For videos
        $selectedItems = json_decode($_POST['selected_videos']);
        $mediaType = 'video';
        $mediaPath = '../uploads/videos/'; // Path to videos folder
    } else {
        echo "No files selected for download.";
        exit;
    }

    // Check if any files are selected
    if (empty($selectedItems)) {
        echo "No files selected for download.";
        exit;
    }

    // Create a zip file
    $zip = new ZipArchive();
    $zipFileName = $mediaType . "_files_" . time() . ".zip";
    $zipFilePath = sys_get_temp_dir() . "/" . $zipFileName;

    if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
        exit("Cannot open <$zipFileName>\n");
    }

    // Add selected files to the zip
    foreach ($selectedItems as $fileName) {
        $filePath = $mediaPath . basename($fileName);
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($fileName));
        }
    }

    // Close the zip file
    $zip->close();

    // Set headers to force download
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $zipFileName);
    header('Content-Length: ' . filesize($zipFilePath));

    // Read the zip file and send it to the user
    readfile($zipFilePath);

    // Delete the zip file after download
    unlink($zipFilePath);

    exit;
} else {
    echo "Invalid request method.";
}
