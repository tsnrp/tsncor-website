<?php

// Time, in seconds, for which award images should be cached on the server
$DEFAULT_CACHE_TIME_SECONDS = 60 * 60 * 24 * 7 * 2; // 2 weeks

$TSNCOR_DEPLOYMENT_WEB_APP_URL = "https://script.google.com/macros/s/AKfycbzspyIb_DzhrM8mQPPKrFmnd-BJ3EFMNPzBH6ZoMOAUcXbOWn9WL_88yyl2VISX0J4cHg/exec";

// URL of the API endpoint that gives the image data. This is part of the apps script.
$TSNCOR_IMAGES_URL = $TSNCOR_DEPLOYMENT_WEB_APP_URL . "?images";
// Filename we use to store the image data on the server
$TSNCOR_IMAGES_CACHE_FILE = "images.json";
// For how long should we store the image data on the server? (By default it's the same duration as the default cache duration)
$TSNCOR_IMAGES_CACHE_MAX_AGE_SECONDS = $DEFAULT_CACHE_TIME_SECONDS;
