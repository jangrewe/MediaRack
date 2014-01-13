<?php
include_once 'config.php';

if ($_GET['get'] == 'shows') {

	$sbdb = new SQLite3($sbPath.'/sickbeard.db');

	$shows = $sbdb->query("SELECT tvdb_id AS id, show_name AS name, location FROM tv_shows ORDER BY show_name ASC;");
	$output = array();
	while ($show = $shows->fetchArray()) {
		$rows = $sbdb->query("SELECT season, COUNT(episode_id) AS count FROM tv_episodes WHERE showid = '".$show['id']."' GROUP BY season ORDER BY season ASC");
		$seasons = array();
		while ($row = $rows->fetchArray()) {
			array_push($seasons, array("season" => $row['season'], "count" => $row['count']));
		}
		array_push($output, array(
			"id" => $show['id'],
			"name" => $show['name'], 
			"folder" => str_replace($showsPath.'/', '', $show['location']), 
			"thumb" => cleanName($show['name']),
			"seasons" => $seasons
		));
		unset($seasons);
	}
	echo json_encode($output);
	die;
	
}

if ($_GET['get'] == 'poster' && !empty($_GET['show'])) {

	if($_GET['season'])
		$poster = 'poster/'.cleanName($_GET['show']).'-S'.$_GET['season'].'.jpg';
	else
		$poster = 'poster/'.cleanName($_GET['show']).'.jpg';
	
	if(!file_exists($poster)) {
	
		if($_GET['season'])
			$source = $showsPath.'/'.addslashes($_GET['show']).'/season'.$_GET['season'].'-poster.jpg';
		else
			$source = $showsPath.'/'.addslashes($_GET['show']).'/poster.jpg';
			
		if(!file_exists($source)) {
			$source = 'img/no_poster.jpg';
		}
		$img = new Imagick();
		$img->setOption('jpeg:size', '800x532');
		$img->readImage($source);
		$img->thumbnailImage(150, 0);
		$img->setImageCompression(Imagick::COMPRESSION_JPEG);
		$img->setImageCompressionQuality(80); 
		$img->writeImage($poster);
	}
	
	header('Content-type: image/jpeg');
	header('Content-length: '.filesize($poster));
	$file = @fopen($poster, 'rb');
	if ($file) {
		fpassthru($file);
		exit;
	}
	die;
}

if ($_GET['get'] == 'fanart' && !empty($_GET['show'])) {

	$fanart = 'fanart/'.cleanName($_GET['show']).'.jpg';
	
	if(!file_exists($fanart)) {
		$source = $showsPath.'/'.addslashes($_GET['show']).'/fanart.jpg';
		if(!file_exists($source)) {
			$source = 'img/no_fanart.jpg';
		}
		$img = new Imagick();
		$img->setOption('jpeg:size', '1280x720');
		$img->readImage($source);
		$img->thumbnailImage(1280, 720);
		$overlay = new Imagick();
		$overlay->newImage(1280, 720, new ImagickPixel('black'));
		$overlay->setOption('jpeg:size', '1280x720');
		$overlay->setImageOpacity(0.80);
		$img->compositeImage($overlay, imagick::COMPOSITE_OVER, 0, 0);
		$img->setImageCompression(Imagick::COMPRESSION_JPEG);
		$img->setImageCompressionQuality(75); 
		$img->writeImage($fanart);
	}
	
	header('Content-type: image/jpeg');
	header('Content-length: '.filesize($fanart));
	$file = @fopen($fanart, 'rb');
	if ($file) {
		fpassthru($file);
		exit;
	}
	die;
}

if ($_GET['get'] == 'logo' && !empty($_GET['show'])) {

	$logo = 'logo/'.cleanName($_GET['show']).'.png';
	
	if(!file_exists($logo)) {
		$source = $showsPath.'/'.addslashes($_GET['show']).'/logo.png';
		if(!file_exists($source)) {
			header("HTTP/1.0 404 Not Found");
			die;
		}else{
			$img = new Imagick();
			$img->readImage($source);
			$img->thumbnailImage(0, 50);
			$img->writeImage($logo);
		}
	}
	
	header('Content-type: image/jpeg');
	header('Content-length: '.filesize($logo));
	$file = @fopen($logo, 'rb');
	if ($file) {
		fpassthru($file);
		exit;
	}
	die;
}

if ($_GET['get'] == 'episodes' && !empty($_GET['show']) && isset($_GET['season'])) {
	$sbdb = new SQLite3($sbPath.'/sickbeard.db');
	$episodes = $sbdb->query("SELECT episode, name, airdate, status FROM tv_episodes WHERE showid = '".$_GET['show']."' AND season = '".$_GET['season']."' ORDER BY episode ASC;");
	$output = array();
	while ($episode = $episodes->fetchArray()) {
		$date = new DateTime('0001-01-00');
		$date->add(new DateInterval('P'.$episode['airdate'].'D'));
		array_push($output, array("episode" => $episode['episode'], "name" => $episode['name'], "status" => (string)$episode['status'], "airdate" => $date->format('Y-m-d')));
	}
	echo json_encode($output);
	die;
}

function cleanName($show) {
	return preg_replace("/[^a-zA-Z0-9]/", "_", $show);
}

?>