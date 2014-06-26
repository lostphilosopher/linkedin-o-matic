<?php 

include('simple_html_dom.php');

class linkedinOmatic 
{
	private $targetUrl 	= null;
	public $html 		= null;

    function __construct($targetUrl)
    {
        $this->targetUrl = $targetUrl;
        $this->html = file_get_html((string) $targetUrl);
    }	

	// Basics
    public function scrapeBasics () {

		$name 		 	= trim(strip_tags($this->html->find('span.full-name')[0]->plaintext));
		$locality 		= trim(strip_tags($this->html->find('span.locality')[0]->plaintext));
		$industry 		= trim(strip_tags($this->html->find('dd.industry')[0]->plaintext));
		$headline 	 	= trim(strip_tags($this->html->find('p.headline-title')[0]->plaintext));

		$basics = array(
			'name' 		=> $name,
			'locality' 	=> $locality,
			'industry' 	=> $industry,
			'headline' 	=> $headline,
		);

		return $basics;
    }

	// Education
    public function scrapeEducation () {

		$school = trim(strip_tags($this->html->find('h3.summary')[0]->plaintext));    	

		$education = array(
			'school' => $school,
		);

		return $education;
    }

	// Links
    public function scrapeLinks () {

		$webUrls 	= $this->html->find('li.website a');
		foreach ($webUrls as $webUrl) {
			$url 	= trim(strip_tags($webUrl->href));
			$start 	= strpos($url, '=');
			$end 	= strpos($url, '&amp;');
			$length = $end - $start;
			$websiteUrls[] = array(
				'type' => trim(strip_tags($webUrl->plaintext)),
				'url' => substr($url, $start + 1, $length - 1),
			);
		}

		return $websiteUrls;
	}

	// Picture
	public function scrapePicture () {

		$url = trim(strip_tags($this->html->find('img.photo')[0]->src));

		$picture = array(
			'url' => $url,
		);

		return $picture;
	}

	// Employment
	public function scrapeEmployment () {

		$description 	= trim(strip_tags($this->html->find('div.position p.description')[0]->plaintext));
		$titles 		= $this->html->find('div.position span.title');
		$organizations 	= $this->html->find('div.position span.org');
		$locations	 	= $this->html->find('div.position span.location');
		$startDates	 	= $this->html->find('div.position abbr.dtstart');
		$endDates	 	= $this->html->find('div.position abbr.dtend');
		$durations	 	= $this->html->find('div.position span.duration');
		$numberOfJobs   = count($titles);
		$i 				= 0;
		while ($i < $numberOfJobs) {
			$jobs[] = array(
				'title' 		=> trim(strip_tags($titles[$i]->plaintext)), 
				'organization' 	=> trim(strip_tags($organizations[$i]->plaintext)),
				'location' 		=> trim(strip_tags($locations[$i]->plaintext)),
				'startDate' 	=> trim(strip_tags($startDates[$i]->plaintext)),
				'endDate' 		=> trim(strip_tags($endDates[$i]->plaintext)),
				'durations' 	=> str_replace(array('(',')'), '', trim(strip_tags($durations[$i]->plaintext))),
			);
			$i++;
		}		

		return $jobs;
	}

	public function scrapeAll () {

		$profile = array();

		$profile[] = array('basics' 		=> $this->scrapeBasics());
		$profile[] = array('education' 		=> $this->scrapeEducation());
		$profile[] = array('links' 			=> $this->scrapeLinks());
		$profile[] = array('picture' 		=> $this->scrapePicture());
		$profile[] = array('employment' 	=> $this->scrapeEmployment());

		return $profile;
	}
}