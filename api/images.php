<?php
// This code fetches award images from Google Drive and stores them locally on disk.
// It acts as a cache layer between the Google Drive and the website, making the loading of images
// much faster.

include("configuration.php");
include("cache_function.php");

$images_cached = fetch_with_cache($TSNCOR_IMAGES_URL, $TSNCOR_IMAGES_CACHE_FILE, $TSNCOR_IMAGES_CACHE_MAX_AGE_SECONDS, false);

if (!$images_cached) {
    // We tried to get the image information into the cache, but it failed. Now we're screwed.
    // We basically throw an error at the client and give up
    header('HTTP/1.0 500 Internal Server Error');
    header('X-ERROR-MESSAGE: Unable to retrieve requested image file.');
    die();
}

$cache_file = "cache/{$TSNCOR_IMAGES_CACHE_FILE}";
$file_content = file_get_contents($cache_file);
$image_json = json_decode($file_content, true);

$file_path = $_GET["key"]; // This will be the file path of a ribbon image

// Okay this line has a lot going on. The problem is that `$file_path` might have different capitalization from what's in the `$image_json`.
// To fix that, we find all entries in `$image_json` that are the same as the `$file_path` if lowercased, and then grab the first one.
// That'll be the same entry, but with correct casing.
$corrected_path = current(array_filter(array_keys($image_json), function($element) use ($file_path) { return strcmp(strtolower($element), strtolower($file_path)) == 0; }));

$file_key = $image_json[$corrected_path]; // This will be the corresponding Google Drive URL
if (!$file_key) {
    // The provided file path was not found in the list. We'll let the client know they fucked up.
    header('HTTP/1.0 404 not found');
    die();
}

// Now on to get the actual image from GDrive:
$drive_url = "https://drive.google.com/uc?export=view&id={$file_key}"; // URL of file on google drive
$cache_seconds = intval(htmlentities($_GET["max_age"] ?? $DEFAULT_CACHE_TIME_SECONDS));

fetch_with_cache($drive_url, $file_key, $cache_seconds);

// This ends the currently running process, so we don't keep hogging server resources.
die();
