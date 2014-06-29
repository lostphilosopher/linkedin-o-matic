<?php

include('linkedinOmatic.php');

parse_str(implode('&', array_slice($argv, 1)), $_GET);

if (!isset($_GET['target'])) {
	$targetUrl = 'https://www.linkedin.com/in/wyattandersen';
} else {
	$targetUrl = $_GET['target'];
}

$linkedinOmatic = new linkedinOmatic($targetUrl);



// Basics
$name 		= $linkedinOmatic->scrapeBasics()['name'];
$locality 	= $linkedinOmatic->scrapeBasics()['locality'];
$industry 	= $linkedinOmatic->scrapeBasics()['industry'];
$headline 	= $linkedinOmatic->scrapeBasics()['headline'];

// Education
$school 	= $linkedinOmatic->scrapeEducation()['school']; 

// Links
$links = $linkedinOmatic->scrapeLinks();

// Picture
$picture = $linkedinOmatic->scrapePicture()['url'];

// Employment
$employments = $linkedinOmatic->scrapeEmployment();

/*
 * DEMO
 * (Terminal)
 */
echo $name . PHP_EOL;
echo $locality . ' - ' . $industry . PHP_EOL;
echo $picture . PHP_EOL;

echo $headline . PHP_EOL;
echo $employments[0]['description'] . PHP_EOL;

echo $school . PHP_EOL . PHP_EOL;

foreach ($links as $link) {
	echo $link['type'] . ': ' . $link['url'] . PHP_EOL;
}
echo PHP_EOL;

foreach ($employments as $employment) {
	echo $employment['title']. PHP_EOL;
	echo $employment['organization'] . ', ' . $employment['location'] . PHP_EOL;
	echo $employment['startDate'] . ' - ' . $employment['endDate'] . ' (' . $employment['durations'] . ')';
	echo PHP_EOL . PHP_EOL;
}
