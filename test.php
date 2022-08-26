<?php
require('ZanfiYouTube.php');

// initialize the class with a yotube video ID or a youtube video url
// $ZanfiYouTube = new ZanfiYouTube('WaEKXGlfYj8');
$ZanfiYouTube = new ZanfiYouTube('https://www.youtube.com/watch?v=WaEKXGlfYj8');

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// ~~~~~~~~~~~~~~~ Data section ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// leave parameter emtpy to print all data
// or give a path like: playbackTracking.videostatsWatchtimeUrl
// $ZanfiYouTube->printVideoInfo('streamingData.adaptiveFormats');

// print array with vieo thumbnails
// $ZanfiYouTube->printFormatted($ZanfiYouTube->arr_Video_thumbnails);

// print the array with video adaptive formats
$ZanfiYouTube->printFormatted($ZanfiYouTube->arr_Video_adaptive_formats);

// print the array with the best video+audio data
// $ZanfiYouTube->printFormatted($ZanfiYouTube->get_best_both());

// print the array with the best audio data
$ZanfiYouTube->printFormatted($ZanfiYouTube->get_best_audio());

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// ~~~~~~~~~~~~~~~ Download section ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// uncomment where needed

// Starts download of video+audio
// $ZanfiYouTube->download_best_both();

// Starts download of audio
// $ZanfiYouTube->download_best_audio();
// shows the elapsed time for download (use it only after a download...)
// echo('Downloaded ' . $ZanfiYouTube->downloaded_bytes . ' bytes in ' . $ZanfiYouTube->elapsed_time . ' seconds');