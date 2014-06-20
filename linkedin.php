<?php 

include('simple_html_dom.php');

// Create DOM from URL or file
$html = file_get_html('https://www.linkedin.com/in/wyattandersen');

// Basics
$name 		 = $html->find('span.full-name')[0];
$headline 	 = $html->find('p.headline-title')[0];
$education 	 = $html->find('h3.summary')[0];

// Employment
$jobs 	 = $html->find('div.postitle');

echo $name->plaintext . PHP_EOL;
echo $headline->plaintext . PHP_EOL;
echo $education->plaintext . PHP_EOL;
foreach ($jobs as $job) {
	echo strip_tags($job->plaintext) . PHP_EOL;
}
