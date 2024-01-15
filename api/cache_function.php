<?php
// This code fetches some sort of remote endpoint and stores it locally on the webserver.
// It acts as a cache layer between the remote endpoint, making the loading
// much faster.

function fetch_with_cache($remote_url, $local_file, $cache_time_seconds, $send_response=true) {
    set_time_limit(180); // If this code takes longer than 3 minutes it will be cancelled.

    // Now begins the actual part of getting the file.
    $file_path = "cache/{$local_file}";
    $cache_url = dirname($_SERVER['REQUEST_URI']) . '/' . $file_path; // Full path of the file on the local server
    $refresh_cache = true;

    // Ensure the cache directory exists
    if (!is_dir("cache")) {
        mkdir("cache", 0755);
    }

    if (file_exists($file_path)) {
        // We have this file! Let's check if it is still fresh:
        $mod_time = filemtime($file_path);
        if (time() - $mod_time < $cache_time_seconds) {
            $refresh_cache = false;
        }
    }

    if ($refresh_cache) {
        // We have determined earlier that we need to update the cached file, so let's do that now.
        if (!function_exists('curl_init')){
            // If we don't have curl, we can't fetch the files for caching, so we give up.
            error_log("Warning: curl is not available.");
            if ($send_response){
                // We tell the client to find the file at its original location.
                header("Location: {$remote_url}");
                die();
            }
            return false;
        }
        $out_file = fopen($file_path, 'w');

        $curl = curl_init($remote_url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // follow redirects
        curl_setopt($curl, CURLOPT_FILE, $out_file); // save to the correct file path
        $res = curl_exec($curl); // actually execute the command

        // The result value indicates whether the curl was successful. If it wasn't, let's log the error.
        if (!$res) {
            error_log("Curl failed");
            error_log(curl_error($curl));
            curl_close($curl);
            fclose($out_file);
            if ($send_response){
                // Since the curl wasn't successful, we let the client know where to find the original file.
                header("Location: {$remote_url}");
                die();
            }
            return false;
        }

        if (curl_getinfo($curl, CURLINFO_RESPONSE_CODE) != 200) {
            // In this case the request did go through but the response indicates some sort of error.
            curl_close($curl);
            fclose($out_file);
            // This also means the file we just saved is probably useless, so let's delete it.
            unlink($file_path);
            if ($send_response){
                // Since the curl wasn't successful, we let the client know where to find the original file.
                header("Location: {$remote_url}");
                die();
            }
            return false;
        }
        curl_close($curl);
        fclose($out_file);
    }

    // We only get this far if the caching was successful. So now we're sure we have the file.
    if ($send_response){
        // Finally, tell the client where to find the cached file.
        header("Location: {$cache_url}");
    }
    return true;
}
