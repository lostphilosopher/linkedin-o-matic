<?php

include('linkedinOmatic.php');

$linkedinOmatic = new linkedinOmatic('https://www.linkedin.com/in/wyattandersen');

// Basics
$name 		= $linkedinOmatic->scrapeBasics()['name'];
$locality 	= $linkedinOmatic->scrapeBasics()['locality'];
$industry 	= $linkedinOmatic->scrapeBasics()['industry'];
$headline 	= $linkedinOmatic->scrapeBasics()['headline'];

/*
 * DEMO
 * (Terminal)
 */
echo $name . PHP_EOL;
echo $locality . ' - ' . $industry . PHP_EOL;
//echo $pictureUrl . PHP_EOL;

echo $headline . PHP_EOL;
//echo $description . PHP_EOL;

/*
echo $education . PHP_EOL . PHP_EOL;

foreach ($websiteUrls as $websiteUrl) {
	echo $websiteUrl['type'] . ': ' . $websiteUrl['url'] . PHP_EOL;
}
echo PHP_EOL;

foreach ($jobs as $job) {
	echo $job['title']. PHP_EOL;
	echo $job['organization'] . ', ' . $job['location'] . PHP_EOL;
	echo $job['startDate'] . ' - ' . $job['endDate'] . ' (' . $job['durations'] . ')';
	echo PHP_EOL . PHP_EOL;
}
*/