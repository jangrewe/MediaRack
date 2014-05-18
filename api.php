<?php
include_once 'config.php';

if ($_GET['get'] == 'shows' && $_GET['limit'] && isset($_GET['offset'])) {

	$output = array();
	$data = querySB('shows');
	uasort($data, function($a, $b) {
		return strcmp($a['show_name'], $b['show_name']);
	});
	$shows = array_slice($data, $_GET['offset'], $_GET['limit'], true);
	foreach($shows as $key => $show) {
		$dataShow = querySB('show', $key);		
		$dataSeasons = querySB('show.seasons', $key);
		$seasons = array();
		foreach($dataSeasons as $season => $episodes) {
			array_push($seasons, array("season" => $season, "count" => count($episodes)));
		}
		array_push($output, array(
			"id" => $key,
			"name" => $show['show_name'],
			"folder" => str_replace($showsPath.'/', '', $dataShow['location']),
			"thumb" => cleanName($show['show_name']),
			"seasons" => $seasons
		));
		unset($seasons);
	}
	echo json_encode($output);
	die;

}


if ($_GET['get'] == 'movies' && $_GET['limit'] && isset($_GET['offset'])) {

	$cpdb = new PDO('sqlite:'.$cpPath.'/couchpotato.db');
	$cpdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$movies = $cpdb->query("SELECT l.identifier AS imdb, lt.title, l.year, l.tagline, l.plot, s.label AS status FROM library AS l
		JOIN librarytitle AS lt ON l.id=lt.libraries_id
		JOIN movie AS m on l.id=m.library_id
		JOIN status AS s ON m.status_id=s.id
		WHERE m.status_id = 3 AND `default` = 1
		ORDER BY title ASC LIMIT ".$_GET['limit']." OFFSET ".$_GET['offset'].";");
	$output = array();
	foreach ($movies as $movie) {
		array_push($output, array(
			"imdb" => $movie['imdb'],
			"title" => $movie['title'],
			"year" => $movie['year'],
			"tagline" => $movie['tagline'],
			"plot" => $movie['plot'],
			"status" => $movie['status']
		));
	}
	echo json_encode($output);
	die;

}


if ($_GET['get'] == 'poster' && (!empty($_GET['show']) || !empty($_GET['movie']))) {

	if($_GET['show'] && $_GET['season'])
		$poster = get_absolute_path('poster/'.cleanName($_GET['show']).'-S'.$_GET['season'].'.jpg');
	elseif($_GET['show'])
		$poster = get_absolute_path('poster/'.cleanName($_GET['show']).'.jpg');
	else
		$poster = get_absolute_path('poster/'. cleanName($_GET['movie']).'.jpg');

	if(!file_exists($poster)) {

		if($_GET['show'] && $_GET['season'])
			$source = '/'.get_absolute_path($showsPath.'/'.cleanName($_GET['show'], false).'/season'.$_GET['season'].'-poster.jpg');
		elseif($_GET['show'])
			$source = '/'.get_absolute_path($showsPath.'/'.cleanName($_GET['show'], false).'/poster.jpg');
		else
			$source = '/'.get_absolute_path($moviesPath.'/'.cleanName($_GET['movie'], false).'/'.cleanName($_GET['movie'], false).'-poster.jpg');

		if(file_exists($source)) {
			$img = new Imagick();
			$img->setOption('jpeg:size', '800x532');
			$img->readImage($source);
			$img->thumbnailImage(0, 220);
			$img->setImageCompression(Imagick::COMPRESSION_JPEG);
			$img->setImageCompressionQuality(80);
			$img->writeImage($poster);
		}elseif($_GET['season'] != '') {
			header("HTTP/1.0 404 Not Found");
			die;
		}else{
			$poster = 'img/no_poster.jpg';
		}
	}

	header('Content-type: image/jpeg');
	header('Content-length: '.filesize($poster));
	readfile($poster);
	die;
}


if ($_GET['get'] == 'fanart' && (!empty($_GET['show']) || !empty($_GET['movie']))) {

	if($_GET['show'])
		$fanart = get_absolute_path('fanart/'. cleanName($_GET['show']).'.jpg');
	else
		$fanart = get_absolute_path('fanart/'. cleanName($_GET['movie']).'.jpg');


	if(!file_exists($fanart)) {

		if($_GET['show'])
			$source = '/'.get_absolute_path($showsPath.'/'.cleanName($_GET['show'], false).'/fanart.jpg');
		else
			$source = '/'.get_absolute_path($moviesPath.'/'.cleanName($_GET['movie'], false).'/'.cleanName($_GET['movie'], false).'-fanart.jpg');

		if(file_exists($source)) {
			$img = new Imagick();
			$img->setOption('jpeg:size', '1024x576');
			$img->readImage($source);
			$img->thumbnailImage(1024, 0);
			$overlay = new Imagick();
			$overlay->newImage(1024, 576, new ImagickPixel('black'));
			$overlay->setOption('jpeg:size', '1024x576');
			$overlay->setImageOpacity(0.80);
			$img->compositeImage($overlay, imagick::COMPOSITE_OVER, 0, 0);
			$img->setImageCompression(Imagick::COMPRESSION_JPEG);
			$img->setImageCompressionQuality(75);
			$img->writeImage($fanart);
		}else{
			$fanart = 'img/no_fanart.jpg';
		}
	}

	header('Content-type: image/jpeg');
	header('Content-length: '.filesize($fanart));
	readfile($fanart);
	die;
}


if ($_GET['get'] == 'logo' && (!empty($_GET['show']) || !empty($_GET['movie']))) {

	if($_GET['show'])
		$logo = get_absolute_path('logo/'.cleanName($_GET['show']).'.png');
	else
		$logo = get_absolute_path('logo/'.cleanName($_GET['movie']).'.png');

	if(!file_exists($logo)) {

		if($_GET['show'])
			$source = '/'.get_absolute_path($showsPath.'/'.cleanName($_GET['show'], false).'/clearlogo.png');
		else
			$source = '/'.get_absolute_path($moviesPath.'/'.cleanName($_GET['movie'], false).'/'.cleanName($_GET['movie'], false).'-clearlogo.png');

		if(file_exists($source)) {
			$img = new Imagick();
			$img->readImage($source);
			$img->thumbnailImage(0, 50);
			$img->writeImage($logo);
		}else{
			header("HTTP/1.0 404 Not Found");
			die;
		}
	}

	header('Content-type: image/png');
	header('Content-length: '.filesize($logo));
	readfile($logo);
	die;
}


if ($_GET['get'] == 'episodes' && !empty($_GET['show']) && isset($_GET['season'])) {
	$output = array();
	if($json = file_get_contents('http://'.$sb['host'].':'.$sb['port'].$sb['path'].'/api/'.$sb['key'].'/?cmd=show.seasons&tvdbid='.$_GET['show'].'&season='.$_GET['season'])) {
		$data = current(json_decode($json, true));
		foreach ($data as $key => $episode) {
			array_push($output, array("episode" => $key, "name" => $episode['name'], "status" => $episode['status'], "airdate" => $episode['airdate']));
		}
	}
	echo json_encode($output);
	die;
}


if ($_GET['get'] == 'latest' && $_GET['type'] == 'shows') {
	
	//$eps = $sbdb->query("SELECT s.show_name, ep.name, ep.episode, ep.season, ep.airdate FROM tv_episodes AS ep JOIN tv_shows AS s ON ep.showid=s.tvdb_id WHERE ep.status LIKE '%4' ORDER BY ep.airdate DESC LIMIT 10;");
	$eps = querySB('history');
	$output = array();
	foreach ($eps as $ep) {
		if($ep['status'] == 'Downloaded') {
			array_push($output, array("show" => $ep['show_name'], "episode" => "S".str_pad($ep['season'], 2, '0', STR_PAD_LEFT)."E".str_pad($ep['episode'], 2, '0', STR_PAD_LEFT), "airdate" => $ep['date']));
		}
	}
	$output = array_slice($output, 0, 10);
	echo json_encode($output);
	die;
}

if ($_GET['get'] == 'latest' && $_GET['type'] == 'movies') {
	$cpdb = new PDO('sqlite:'.$cpPath.'/couchpotato.db');
	$cpdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$movies = $cpdb->query("SELECT lt.title, l.year FROM library AS l
		JOIN librarytitle AS lt ON l.id=lt.libraries_id
		JOIN movie AS m on l.id=m.library_id
		JOIN status AS s ON m.status_id=s.id
		WHERE m.status_id = 3 AND `default` = 1
		ORDER BY m.last_edit DESC LIMIT 10;");
	$output = array();
	foreach ($movies as $movie) {
		array_push($output, array("movie" => $movie['title'].' ('.$movie['year'].')'));
	}
	echo json_encode($output);
	die;
}


function cleanName($name, $strict = true) {
	if($strict == true)
		return preg_replace("/[^a-zA-Z0-9]/", "_", $name);
	else
		return preg_replace("/:/", "", $name);
}


function get_absolute_path($path) {
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
	$absolutes = array();
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = $part;
		}
	}
	return implode(DIRECTORY_SEPARATOR, $absolutes);
}

function querySB($cmd, $id = '', $season = '') {
	global $sb, $cacheTTL;
	if($id == '') {
		$cache = './cache/'.$cmd.'.json';
	}else{
		$cache = './cache/'.$cmd.'-'.$id.'.json';
	}
	if (!file_exists($cache) || filemtime($cache) < time()-$cacheTTL) {
		file_put_contents($cache, file_get_contents('http://'.$sb['host'].':'.$sb['port'].$sb['path'].'/api/'.$sb['key'].'/?cmd='.$cmd.'&tvdbid='.$id.'&season='.$season));
	}
	$json = file_get_contents($cache);
	$data = current(json_decode($json, true));
	return $data;
}

?>