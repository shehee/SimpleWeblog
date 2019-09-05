<?php
	/*
	// Author: Ryan Shehee
	// Date: 2019-09-05 14:19:00
	// Version: 0.2
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
	// Define variables: title of blog, timezone, and logfile if desired
	*/
	$title = 'Simple Weblog';
	$timezone = 'America/Los_Angeles';
	$logfile = '';
	$adminIP = '';

	/*
	// Set timezone
	*/
	date_default_timezone_set($timezone);

	/*
	// Write log entry
	*/
	$geolocArray = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
	if(is_writable($logfile) && $_SERVER['REMOTE_ADDR'] != $adminIP) {
		file_put_contents($logfile, date('Y-m-d H:i:s').' from '.$_SERVER['REMOTE_ADDR']." (".$geolocArray['geoplugin_city'].", ".$geolocArray['geoplugin_region']." : ".$geolocArray['geoplugin_latitude'].", ".$geolocArray['geoplugin_longitude'].")".PHP_EOL, FILE_APPEND);
	}

	/*
	// Read all markdown posts and convert them into HTML
	*/
	require_once('Parsedown/Parsedown.php');
	$dir = glob("md/*.md");
	foreach($dir as $filename) {
		$contents = file_get_contents($filename);
		$postFilesize = filesize($filename);

		$filenameArray = explode(".", $filename);
		$filenameArray = explode("/", $filenameArray[0]);
		$filenameArray = explode("_", $filenameArray[1]);
		$filenameArray[1] = str_replace("-", ":", $filenameArray[1]);
		$timestamp = implode(" ", $filenameArray);
		$timestamp = strtotime($timestamp);

		$postYear = date('Y', $timestamp);
		$postMonth = date('F', $timestamp);
		$postDate = date('jS', $timestamp);
		$postDay = date('l', $timestamp);
		$postHour = date('g', $timestamp);
		$postMinute = date('i', $timestamp);
		$postSecond = date('s', $timestamp);
		$postMeridiem = date('A T', $timestamp);

		$html .= '<section class="post">';
		$html .= '<div class="datetime">';
		$html .= '<span class="day">'.$postDay.'</span><span class="comma">,&nbsp;</span>';
		$html .= '<span class="month">'.$postMonth.'</span><span class="space">&nbsp;</span>';
		$html .= '<span class="date">'.$postDate.'</span><span class="space">&nbsp;</span>';
		$html .= '<span class="year">'.$postYear.'</span><span class="space">&nbsp;</span>';
		$html .= '<span class="hour">'.$postHour.'</span><span class="colon">:</span>';
		$html .= '<span class="minute">'.$postMinute.'</span><span class="colon">:</span>';
		$html .= '<span class="second">'.$postSecond.'</span><span class="space">&nbsp;</span>';
		$html .= '<span class="meridiem">'.$postMeridiem.'</span>';
		$html .= '</div>';

		$Parsedown = new Parsedown();
		$html .= $Parsedown->text($contents);

		$html .= '<div class="filesize">'.$postFilesize.' bytes</div>';
		$html .= "</section>";
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title." - ".basename(__FILE__);?></title>
		<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <style>
            * { margin:0; padding:0; font-family:'Roboto',sans-serif; }
            html, body { font-size:12pt; height: 100%; width:100%; }
			h1, h2, h3, h4, h5, h6, p { padding:0.5rem 0; }
			ol, ul { padding:0.5rem 2rem; }
			article, section { margin:auto; padding:0.5rem 0; width:80%; }
			header { background-color:#FFF; display:block; outline:1px solid #333; position:fixed; padding:0.5rem 10%; top:0; width:100%; }
			footer { background-color:#FFF; bottom:0; color:#333; display:block; font-size:.8rem; outline:1px solid #333; padding:0.5rem 10%; position:fixed; width:100%; }
			section { border-top:1px solid #CCC; }
			img { width:100%; }
			.datetime { color:#666; font-size:.9rem; padding:0.5rem 0; }
			.filesize { color:#CCC; font-size:.7rem; padding:0.5rem 0; }
			.right { text-align:right; }
        </style>
    </head>
    <body>
		<header>
			<h1><?php echo $title;?></h1>
			<!-- <nav></nav> -->
		</header>
		<br>
		<br>
		<br>
		<br>
		<article>
		<p class="datetime"><?php echo date('l, F jS Y g:i:s A T'); ?></p>
		<p>Hello and welcome! This is a simple weblog, or "slog."</p></article>
		<?php echo $html; ?>
		<br>
		<br>
		<br>
		<footer><p>Updated <?php echo date('l, F jS Y g:i:s A T', $timestamp); ?>. <a href="https://github.com/shehee/SimpleWeblog">Simple Weblog</a> is licensed under the <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">GNU GPLv3</a>. <a href="https://parsedown.org/">Parsedown</a> is licensed under the <a href="https://github.com/erusev/parsedown/blob/master/LICENSE.txt">The MIT License</a>. Page generated in 
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
		?> seconds.</p></footer>
    </body>
</html>