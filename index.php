<?php
	/*
	// Author: Ryan Shehee
	// Date: 2019-09-29 17:50:00
	// Version: 0.7
	//
	// Description: This is a simple weblog that is 
	// clean, simple, easy, and portable!
	//
	// License: GNU GENERAL PUBLIC LICENSE, Version 3
	*/

	/*
	// Start benchmark
	*/
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;

	/*
	// Disable caching
	*/
	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.

	/*
	// Define variables: title of blog, timezone, and logfile if desired
	*/
	$configFilename = 'config.php';
	if(file_exists($configFilename)) {
		require_once($configFilename);
	}

	/*
	// Set timezone
	*/
	date_default_timezone_set($timezone);

	/*
	// Write log entry
	*/
	$geolocArray = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
	if(is_writable($logFilename) && $_SERVER['REMOTE_ADDR'] != $adminIP) {
		file_put_contents($logFilename, date('Y-m-d H:i:s').' from '.$_SERVER['REMOTE_ADDR']." (".$geolocArray['geoplugin_city'].", ".$geolocArray['geoplugin_region']." : ".$geolocArray['geoplugin_latitude'].", ".$geolocArray['geoplugin_longitude'].")".PHP_EOL, FILE_APPEND);
	}

	/*
	// Function for making the filename timestamp
	*/
	function makeTimestamp(&$markdownArray) {
			$filename = $markdownArray['filename'];
			$filenameArray = explode(".", $filename);
			$filenameArray = explode("/", $filenameArray[0]);
			$filenameArray = explode("_", $filenameArray[1]);
			$filenameArray[1] = str_replace("-", ":", $filenameArray[1]);
			if($filenameArray[1]) {
				$timestamp = implode(" ", $filenameArray);
				$timestamp = strtotime($timestamp);
			} else {
				$timestamp = $markdownArray['filemtime'];
			}
			return $timestamp;
	}

	/*
	// Function for formatting sections
	*/
	function markdownToHTML(&$markdownArray) {
		if(file_exists($markdownArray['filename'])) {
			$timestamp = makeTimestamp($markdownArray);

			$year = date('Y', $timestamp);
			$month = date('F', $timestamp);
			$date = date('jS', $timestamp);
			$day = date('l', $timestamp);
			$hour = date('g', $timestamp);
			$minute = date('i', $timestamp);
			$second = date('s', $timestamp);
			$meridiem = date('A T', $timestamp);

			$contents = file_get_contents($markdownArray['filename']);
			$filesize = filesize($markdownArray['filename']);

			$htmlString .= !empty($markdownArray['tag']) ? "<".$markdownArray['tag'] : "<section";
			$htmlString .= !empty($markdownArray['class']) ? ' class="'.$markdownArray['class'].'" ' : NULL;
			$htmlString .= ' id="' . (!empty($markdownArray['id']) ? $markdownArray['id'] : date('c', $timestamp) ).'">';

			$Parsedown = new Parsedown();
			$htmlString .= $Parsedown->text($contents);

			$htmlString .= '<footer>';
			$htmlString .= '<span class="datetime">';
			$htmlString .= '<span class="day">'.$day.'</span><span class="comma">,&nbsp;</span>';
			$htmlString .= '<span class="month">'.$month.'</span><span class="space">&nbsp;</span>';
			$htmlString .= '<span class="date">'.$date.'</span><span class="space">&nbsp;</span>';
			$htmlString .= '<span class="year">'.$year.'</span><span class="space">&nbsp;</span>';
			$htmlString .= '<span class="hour">'.$hour.'</span><span class="colon">:</span>';
			$htmlString .= '<span class="minute">'.$minute.'</span><span class="colon">:</span>';
			$htmlString .= '<span class="second">'.$second.'</span><span class="space">&nbsp;</span>';
			$htmlString .= '<span class="meridiem">'.$meridiem.'</span>';
			$htmlString .= '</span>';
			$htmlString .= '<span class="filesize">'.$filesize.' bytes</span>';
			$htmlString .= '</footer>';
			$htmlString .= "</". (!empty($markdownArray['tag']) ? $markdownArray['tag'] : "section") .">";

			return $htmlString;
		}
	}

	/*
	// Read all markdown posts and convert them into HTML
	*/
	$parsedownFilename = 'Parsedown/Parsedown.php';
	if(file_exists($parsedownFilename)) {
		require_once($parsedownFilename);
	}
	/*
	// Start with Welcome message
	*/
	if(file_exists($welcomeFilename)) {
		$markdownArray = array(
				'filename' => $welcomeFilename,
				'tag' => 'article',
				'id' => 'welcome',
				'filemtime' => filemtime($welcomeFilename)
			);
		$htmlString .= markdownToHTML($markdownArray);
	}
	/*
	// Then add FAQ
	*/
	if(file_exists($faqFilename)) {
		$markdownArray = array(
				'filename' => $faqFilename,
				'tag' => 'section',
				'id' => 'faq',
				'filemtime' => filemtime($faqFilename)
			);
		$htmlString .= markdownToHTML($markdownArray);
	}
	/*
	// Finally convert any posts
	*/
	$dir = glob("md/*.md");
	$count = 0;
	$total = count($dir);
	if(is_array($dir)) {
		foreach($dir as $postFilename) {
            $markdownArray = array(
                'filename' => $postFilename,
                'tag' => 'section',
                'id' => NULL,
                'filemtime' => filemtime($postFilename)
            );
			$count++;
			if($count == $total) {
				$markdownArray['id'] = 'latest';
			}
			$htmlString .= markdownToHTML($markdownArray);
		}
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title." - Updated ".date('D, M jS Y g:i:s A T', $markdownArray['filemtime']); ?></title>
		<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <style>
            * { margin:0; padding:0; font-family:'Roboto',sans-serif; }
            html, body { font-size:12pt; height: 100%; width:100%; }
            html { background-color:#FFF; }
            body { background-color:#FFF; color:#000; }
            body > header, body > footer { background-color:#FFF; display:block; outline:1px solid #000; padding:0.5rem 10%; position:fixed; width:80%; z-index:1; }
			body > header { top:0; }
			body > footer { bottom:0; font-size:0.8rem; }
			header h1 { float:left; }
			body > header p, body >header nav { text-align:right; }
			article, section { margin:auto; padding:0.5rem 0; width:80%; }
			article { margin-top:5rem; }
			section { border-top:1px solid #CCC; }
			article footer, section footer { font-size:0.8rem; padding:0.5rem 0; width:100%; }
			h1, h2, h3, h4, h5, h6, p { padding:0.5rem 0; }
			ol, ul { padding:0.5rem 2rem; }
			img { display:block; margin:0 auto; max-width:100%; }
			.filesize { color:#CCC; float:right; }
            a:link { color:#00F; border-bottom:1px solid #00F; border-radius:1rem; text-decoration:none; padding:0 0.5rem; } /* unvisited link */
            a:visited { color:#00B; } /* visited link */
            a:hover { color:#008; border-top:1px solid #008; border-bottom:none; } /* mouse over link */
            a:active { color:#F00; border-top:1px solid #F00; border-bottom:none; } /* selected link */
        </style>
    </head>
    <body>
		<header>
			<h1><?php echo $title; ?></h1>
			<p class="datetime"><?php echo date('l, F jS Y g:i:s A T'); ?></p>
			<nav class="right"><a href="#welcome">Welcome</a>&nbsp;<a href="#faq">FAQ</a>&nbsp;<a href="#latest">Latest</a></nav>
		</header>
		<?php echo $htmlString; ?>
		<br>
		<br>
		<br>
		<br>
		<footer><p>Updated <?php echo date('l, F jS Y g:i:s A T', $markdownArray['filemtime']); ?>. Page generated in 
		<?php
			/*
			// End benchmark
			*/
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$finish = $time;
			$total_time = round(($finish - $start), 4);
			echo $total_time;
		?> seconds. <a href="https://github.com/shehee/Simple_Weblog">Simple Weblog</a> is licensed under the <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">GNU GPLv3</a>. <a href="https://parsedown.org/">Parsedown</a> is licensed under the <a href="https://github.com/erusev/parsedown/blob/master/LICENSE.txt">The MIT License</a>.</p></footer>
    </body>
</html>