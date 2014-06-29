<?php 

include('simple_html_dom.php');

class linkedinOmatic 
{
	private $targetUrl 	= null;
	public $html 		= null;

	// HTML selection queries
	// Must be updated as LinkedIn changes their markup
	// Basics
	const NAME_QUERY 			= 'span.full-name';
	const LOCALITY_QUERY 		= 'span.locality';
	const INDUSTRY_QUERY 		= 'dd.industry';
	const HEADLINE_QUERY 		= 'p.title';
	// Education
	const SCHOOL_QUERY			= 'h4.summary';
	// Links
	const LINKS_QUERY 			= 'div.profile-overview-content li a';
	// Picture
	const PICTURE_QUERY			= 'div.profile-picture img';
	// Education
	const DESCRIPTIONS_QUERY 	= 'p.description';
	const TITLES_QUERY 			= 'div.background-experience header h4';
	const ORGANIZATIONS_QUERY 	= 'div.background-experience header h5';
	const LOCATIONS_QUERY 		= 'div.background-experience span.locality';
	const DURATIONS_QUERY 		= 'span.experience-date-locale time';

    function __construct($targetUrl)
    {
        $this->targetUrl = $targetUrl;
        $this->html = file_get_html((string) $targetUrl);
    }	

	// Basics
    public function scrapeBasics () {

		$name 		 	= trim(strip_tags($this->html->find(self::NAME_QUERY)[0]->plaintext));
		$locality 		= trim(strip_tags($this->html->find(self::LOCALITY_QUERY)[0]->plaintext));
		$industry 		= trim(strip_tags($this->html->find(self::INDUSTRY_QUERY)[0]->plaintext));
		$headline 	 	= trim(strip_tags($this->html->find(self::HEADLINE_QUERY)[0]->plaintext));

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

		$school = trim(strip_tags($this->html->find(self::SCHOOL_QUERY)[0]->plaintext));    	

		$education = array(
			'school' => $school,
		);

		return $education;
    }

	// Links
    public function scrapeLinks () {

		$webUrls 	= $this->html->find(self::LINKS_QUERY);

		foreach ($webUrls as $webUrl) {
			$url 	= trim(strip_tags($webUrl->href));
			$start 	= strpos($url, '='); // Must be the beginning of the url piece of the LinkedIn url
			$end 	= strpos($url, '&url'); // Must be the last character following the url 
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

		$url = trim(strip_tags($this->html->find('div.profile-picture img')[0]->src));

		$picture = array(
			'url' => $url,
		);

		return $picture;
	}

	// Employment
	public function scrapeEmployment () {

		$description 	= trim(strip_tags($this->html->find(self::DESCRIPTIONS_QUERY)[0]->plaintext));
		$titles 		= $this->html->find(self::TITLES_QUERY);
		$organizations 	= $this->html->find(self::ORGANIZATIONS_QUERY);
		$locations	 	= $this->html->find(self::LOCATIONS_QUERY);
		$durations	 	= $this->html->find(self::DURATIONS_QUERY);
		$numberOfJobs   = count($titles);
		$i 				= 0;
		$n 				= 0;
		while ($i < $numberOfJobs) {
			$i == 0 ?: $description = ''; // As of 06/29/14 description is only available for the most recent job
			$jobs[] = array(
				'title' 		=> trim(strip_tags($titles[$i]->plaintext)),
				'organization' 	=> trim(strip_tags($organizations[$i+1]->plaintext)),
				'location' 		=> trim(strip_tags($locations[$i]->plaintext)),
				'description' 	=> trim(strip_tags($description)),
				'startDate' 	=> trim(strip_tags($durations[$n]->plaintext)),
				'endDate' 		=> trim(strip_tags($durations[$n+1]->plaintext)),
				'durations' 	=> str_replace(array('(',')'), '', trim(strip_tags($durations[$n + 2]->plaintext))),
			);
			$i = $i + 1;
			$n = $n + 3; // As of 06/29/14 LinkedIn isn't provide unique date identifiers
		}

		return $jobs;
	}

	// Wrapper method for all scraping methods
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