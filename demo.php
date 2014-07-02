<?php

/**
 * DEMO
 * Terminal use: php demo.php target=YOUR_PUBLIC_LINKEDIN_URL_HERE
 */

include('linkedinOmatic.php');

// Gets the url provided in the terminal and defaults if one isn't available
parse_str(implode('&', array_slice($argv, 1)), $_GET);
if (!isset($_GET['target'])) {
	$targetUrl = 'https://www.linkedin.com/in/wyattandersen';
} else {
	$targetUrl = $_GET['target'];
}

// Initializes class with a target public url
$linkedinOmatic = new linkedinOmatic($targetUrl);

// Basics
$name 		= $linkedinOmatic->scrapeBasics()['name'];
$locality 	= $linkedinOmatic->scrapeBasics()['locality'];
$industry 	= $linkedinOmatic->scrapeBasics()['industry'];
$headline 	= $linkedinOmatic->scrapeBasics()['headline'];

echo $name . PHP_EOL;
echo $locality . ' - ' . $industry . PHP_EOL;
echo $headline . PHP_EOL;

$employments = $linkedinOmatic->scrapeEmployment();
if ($employments) {
	echo $employments[0]['description'] . PHP_EOL . PHP_EOL;
}
// Education
$school 	= $linkedinOmatic->scrapeEducation()['school']; 

echo $school . PHP_EOL . PHP_EOL;

// Links
$links = $linkedinOmatic->scrapeLinks();

foreach ($links as $link) {
	echo $link['type'] . ': ' . $link['url'] . PHP_EOL;
}
echo PHP_EOL;

// Picture
$picture = $linkedinOmatic->scrapePicture()['url'];

echo $picture . PHP_EOL . PHP_EOL;

// Employment
foreach ($employments as $employment) {
	echo $employment['title']. PHP_EOL;
	echo $employment['organization'] . ', ' . $employment['location'] . PHP_EOL;
	echo $employment['startDate'] . ' - ' . $employment['endDate'] . ' (' . $employment['durations'] . ')';
	echo PHP_EOL . PHP_EOL;
}
