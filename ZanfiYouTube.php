<?php
class ZanfiYouTube{

	var $video_id;
	var $arrBestBoth;
	var $arrBestAudio;
	var $arr_Video_info;
	var $arr_Video_formats;
	var $arr_Video_adaptive_formats;
	var $arr_Video_thumbnails;
	var $youtube_html;
	var $base_js;
	var $elapsed_start;
	var $elapsed_time;
	var $downloaded_bytes;
	var $downloaded_file_name;
	
	function __construct($vId = null){
		
		if(is_null($vId) || $vId == '') {
			
			throw new Exception("Please pass a youtube video to fetch.");
			
			return(false);
			
		}
		
        $this->video_id = $this->get_video_id($vId);
		
		// itags for video+audio stream good quality to worser
		$this->arrBestBoth = array(37,46,22,18);
		
		// itags for audio stream good quality to worser
		$this->arrBestAudio = array(141,140,251,171);
		
		// uses https://m.youtube.com/watch?v=[VIDEO_ID] to get the html with the streaming data
		// signature must be deciphered
		$this->getVideoInfo();
		
		// uses https://www.youtube.com/youtubei/v1/player?key=AIzaSyA8eiZmM1FaDVjRy-df2KTyQ_vz_yYM39w to get the streaming data
		// urls ready, no signature to decipher (preferred)
		// $this->getVideoInfoDroid();
		
		(isset($this->arr_Video_info['streamingData']['adaptiveFormats'])) ? $this->arr_Video_adaptive_formats = $this->arr_Video_info['streamingData']['adaptiveFormats'] : $this->arr_Video_adaptive_formats = [];
		
		$this->check_url_signed($this->arr_Video_adaptive_formats);
		
		(isset($this->arr_Video_info['streamingData']['formats'])) ? $this->arr_Video_formats = $this->arr_Video_info['streamingData']['formats'] : $this->arr_Video_formats = [];
		
		$this->check_url_signed($this->arr_Video_formats);
		
		(isset($this->arr_Video_info['videoDetails']['thumbnail'])) ? $this->arr_Video_thumbnails = $this->arr_Video_info['videoDetails']['thumbnail'] : $this->arr_Video_thumbnails = [];
		
    }
	
	function getTime(){
		
		$a = explode (' ',microtime());
		return(double) $a[0] + $a[1];
		
	}
	
	function exec_time(){
		
		$endtime = $this->getTime();
		
		$this->elapsed_time = $endtime-$this->elapsed_start;
		
	}
	
	function get_video_id($str){
		
		// if url passed
		if(strpos($str, '://') !== false){
			
			parse_str( parse_url( $str, PHP_URL_QUERY ), $my_array_of_vars );

			// 2 possibilities ... ? !
			if(isset($my_array_of_vars['v'])) return($my_array_of_vars['v']);
			
			if(isset($my_array_of_vars['vi'])) return($my_array_of_vars['vi']);
			
		}else{
			
			return($str);
			
		}

	}
	
	function get_string_between($string, $start, $end){
	
		$string = ' ' . $string;
		
		$ini = strpos($string, $start);
		
		if ($ini == 0) return '';
		
		$ini += strlen($start);
		
		$len = strpos($string, $end, $ini) - $ini;
		
		return substr($string, $ini, $len);
		
	}
	
	function printVideoInfo($path = null){
		
		$arr = $this->arr_Video_info;
		
		if(!is_null($path)){
			
			$arrPath = explode('.', $path);
			
			foreach($arrPath as $part){
					
				$arr = $arr[$part];
				
			}
			
		}
		
		echo('<pre>');
		var_dump($arr);
		echo('</pre>');
		
	}
	
	function printFormatted($arr){
		
		echo('<pre>');
		var_dump($arr);
		echo('</pre>');
		
	}
	
	function get_best_both(){
		
		$break = null;
		
		foreach($this->arr_Video_formats as $val){
			
			foreach($this->arrBestBoth as $bb){
				
				if($val['itag'] == $bb){
					
					return($val);
					
					$break = 1;
					
				}
				
				if($break) break;
				
			}
			
			if($break) break;
			
		}		
		
	}
	
	function download_best_both(){
		
		$break = null;
		
		foreach($this->arr_Video_formats as $val){
			
			foreach($this->arrBestBoth as $bb){
				
				if($val['itag'] == $bb){
					
					$arrMmimeType = explode(';', $val['mimeType']);
					
					$arrExt = explode('/', $arrMmimeType[0]);
					
					$this->download($val['url'], $this->arr_Video_info['videoDetails']['title'] . '.' . $arrExt[1]);
					
					$break = 1;
					
				}
				
				if($break) break;
				
			}
			
			if($break) break;
			
		}
		
	}

	function get_best_audio(){
		
		$break = null;
		
		foreach($this->arr_Video_adaptive_formats as $val){
			
			foreach($this->arrBestAudio as $ba){
				
				if($val['itag'] == $ba){
					
					return($val);
					
					$break = 1;
					
				}
				
				if($break) break;
				
			}
			
			if($break) break;
			
		}		
		
	}
	
	function download_best_audio(){
		
		$break = null;
		
		foreach($this->arr_Video_adaptive_formats as $val){
			
			foreach($this->arrBestAudio as $ba){
				
				if($val['itag'] == $ba){
					
					$arrMmimeType = explode(';', $val['mimeType']);
					
					$arrExt = explode('/', $arrMmimeType[0]);
					
					$this->download($val['url'], $this->arr_Video_info['videoDetails']['title'] . '.' . $arrExt[1]);
					
					$break = 1;
					
				}
				
				if($break) break;
				
			}
			
			if($break) break;
			
		}
		
	}
	
	function getVideoInfoDroid(){

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://www.youtube.com/youtubei/v1/player?key=AIzaSyA8eiZmM1FaDVjRy-df2KTyQ_vz_yYM39w');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '{
											   "context":{
												  "client":{
													 "hl":"en",
													 "clientName":"ANDROID",
													 "clientVersion":"17.29.34",
													 "clientFormFactor":"UNKNOWN_FORM_FACTOR",
													 "clientScreen":"WATCH",
													 "mainAppWebInfo":{
														"graftUrl":"/watch?v='.$this->video_id.'"
													 }
												  },
												  "user":{
													 "lockedSafetyMode":false
												  },
												  "request":{
													 "useSsl":true,
													 "internalExperimentFlags":[
													 ],
													 "consistencyTokenJars":[
													 ]
												  }
											   },
											   "videoId":"'.$this->video_id.'",
											   "playbackContext":{
												  "contentPlaybackContext":{
													 "vis":0,
													 "splay":false,
													 "autoCaptionsDefaultOn":false,
													 "autonavState":"STATE_NONE",
													 "html5Preference":"HTML5_PREF_WANTS",
													 "lactMilliseconds":"-1"
												  }
											   },
											   "racyCheckOk":false,
											   "contentCheckOk":false
											}');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

		$headers = array();
		$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8';
		$headers[] = 'Accept-encoding: gzip, deflate';
		$headers[] = 'Accept-Language: en-US,en;q=0.5';
		$headers[] = 'cache-control: max-age=0';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-Site: same-origin';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Gpc: 1';
		$headers[] = 'Range: bytes=0-';
		$headers[] = 'service-worker-navigation-preload: true';
		$headers[] = 'Upgrade-Insecure-Requests: 1';
		//$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36';
		$headers[] = 'User-Agent: Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17';
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		
		$this->arr_Video_info = json_decode($result, true);;

	}
	
	function getVideoInfo(){

		$this->youtube_html = file_get_contents('https://m.youtube.com/watch?v='.$this->video_id);
		
		$this->get_base_js();
		
		$scr = $this->get_string_between($this->youtube_html, 'var ytInitialPlayerResponse = ', ';</script>');

		$scr = trim($scr," \t\n\r\0\x0B;");
		
		$this->arr_Video_info = json_decode($scr, true);
		
	}	
	
	function get_base_js(){

		$re = '/"jsUrl":"([^"]+)base.js"/s';

		preg_match_all($re, $this->youtube_html, $matches, PREG_SET_ORDER, 0);

		// Print the entire match result
		// echo '<pre>';var_dump($matches[0][1]);echo '</pre>';

		$this->base_js = file_get_contents('https://www.youtube.com'.$matches[0][1].'base.js');
		
	}

	// thanks to https://github.com/Athlon1600/youtube-downloader
    public function decode($signature){
		
        $func_name = $this->parseFunctionName();

        // PHP instructions
        $instructions = (array)$this->parseFunctionCode($func_name);

        foreach ($instructions as $opt) {

            $command = $opt[0];
            $value = $opt[1];

            if ($command == 'swap') {

                $temp = $signature[0];
                $signature[0] = $signature[$value % strlen($signature)];
                $signature[$value] = $temp;
				
            } elseif ($command == 'splice') {
				
                $signature = substr($signature, $value);
				
            } elseif ($command == 'reverse') {
				
                $signature = strrev($signature);
				
            }
        }

        return trim($signature);
		
    }

	// thanks to https://github.com/Athlon1600/youtube-downloader
    public function parseFunctionName(){
		
        if (preg_match('@,\s*encodeURIComponent\((\w{2})@is', $this->base_js, $matches)) {
            $func_name = $matches[1];
            $func_name = preg_quote($func_name);

            return $func_name;

        } else if (preg_match('@(?:\b|[^a-zA-Z0-9$])([a-zA-Z0-9$]{2,3})\s*=\s*function\(\s*a\s*\)\s*{\s*a\s*=\s*a\.split\(\s*""\s*\)@is', $this->base_js, $matches)) {

            return preg_quote($matches[1]);

        }

        return(null);
		
    }

    // convert JS code for signature decipher to PHP code
	// thanks to https://github.com/Athlon1600/youtube-downloader
    public function parseFunctionCode($func_name){
		
        // extract code block from that function
        // single quote in case function name contains $dollar sign
        // xm=function(a){a=a.split("");wm.zO(a,47);wm.vY(a,1);wm.z9(a,68);wm.zO(a,21);wm.z9(a,34);wm.zO(a,16);wm.z9(a,41);return a.join("")};
        if (preg_match('/' . $func_name . '=function\([a-z]+\){(.*?)}/', $this->base_js, $matches)) {

            $js_code = $matches[1];
			//echo('<pre>');var_dump($js_code);echo('</pre>');

            // extract all relevant statements within that block
            // wm.vY(a,1);
            if (preg_match_all('/([a-z0-9$]{2})\.([a-z0-9]{2})\([^,]+,(\d+)\)/i', $js_code, $matches) != false) {

                // wm
                $obj_list = $matches[1];

                // vY
                $func_list = $matches[2];

                // extract javascript code for each one of those statement functions
                preg_match_all('/(' . implode('|', $func_list) . '):function(.*?)\}/m', $this->base_js, $matches2, PREG_SET_ORDER);
				// echo('<pre>');var_dump($matches2);echo('</pre>');
				

                $functions = array();

                // translate each function according to its use
                foreach ($matches2 as $m) {

                    if (strpos($m[2], 'splice') !== false) {
                        $functions[$m[1]] = 'splice';
                    } elseif (strpos($m[2], 'a.length') !== false) {
                        $functions[$m[1]] = 'swap';
                    } elseif (strpos($m[2], 'reverse') !== false) {
                        $functions[$m[1]] = 'reverse';
                    }
                }

                // FINAL STEP! convert it all to instructions set
                $instructions = array();

                foreach ($matches[2] as $index => $name) {
                    $instructions[] = array($functions[$name], $matches[3][$index]);
                }
				// echo('<pre>');var_dump($instructions);echo('</pre>');
                return $instructions;
            }
			
        }

        return(null);
    }
	
	function download($file_url,$destination_path){
		
		// start counting execution time.
		$this->elapsed_start = $this->getTime();
		
		// uncomment line below for very large downloads (to avoid max excedeed time error)
		// set_time_limit(0);
		
		
		$destination_path = $this->filter_filename($destination_path);
		
		$this->downloaded_file_name = $destination_path;

		$fp = fopen($destination_path, "w+");
		
		$ch = curl_init();
		
		$arrUrl = parse_url($file_url);

		curl_setopt($ch, CURLOPT_URL, $file_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		// uncomment the 2 lines below to see progrees in browser
		// curl_setopt($ch, CURLOPT_NOPROGRESS, 0 );
		// curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this, 'downloadProgress')); 
		curl_setopt($ch, CURLOPT_BUFFERSIZE, 1024*4);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

		$headers = array();
		$headers[] = $arrUrl['host'];
		$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8';
		$headers[] = 'Accept-encoding: gzip, deflate';
		$headers[] = 'Accept-Language: en-US,en;q=0.5';
		$headers[] = 'cache-control: max-age=0';
		$headers[] = 'Sec-Fetch-Dest: document';
		$headers[] = 'Sec-Fetch-Mode: navigate';
		$headers[] = 'Sec-Fetch-Site: same-origin';
		$headers[] = 'Sec-Fetch-User: ?1';
		$headers[] = 'Sec-Gpc: 1';
		$headers[] = 'Range: bytes=0-';
		$headers[] = 'service-worker-navigation-preload: true';
		$headers[] = 'Upgrade-Insecure-Requests: 1';
		//$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.102 Safari/537.36';
		$headers[] = 'User-Agent: Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_FILE, $fp);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		
		$info = curl_getinfo($ch);
		$this->downloaded_bytes = $info['size_download'];
		
		curl_close($ch);

		fclose($fp);
		
		$this->exec_time();
		
	}
	
	function downloadProgress ($ch, $download_size, $downloaded_size, $upload_size, $uploaded_size) {
		
		ob_start();
		echo 'download_size: ' . $download_size . '; downloaded_size: ' . $downloaded_size . ';<br>';
		ob_flush();
		ob_end_flush();
		
	}
	
	function check_url_signed(&$arrVideo){
		
		foreach($arrVideo as $key => $val){
			
			if(!isset($val['url']) || (isset($val['url']) && $val['url'] == '' && isset($val['signatureCipher']))){
				
				parse_str($val['signatureCipher'], $strCipher);
				
				$SigDec = $this->decode($strCipher['s']);
				
				$urlSigned = $strCipher['url'] . '&' . $strCipher['sp'] . '=' . $SigDec;
				
				$arrVideo[$key]['url'] = $urlSigned;
				
			}
			
		}
		
	}
	
	function filter_filename($name) {
		
		// remove illegal file system characters https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
		$name = str_replace(array_merge(
			array_map('chr', range(0, 31)),
			array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
		), '', $name);
		// maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		$name= mb_strcut(pathinfo($name, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($name)) . ($ext ? '.' . $ext : '');
		return($name);
		
	}

}