<?php 

include('simple_html_dom.php'); // http://simplehtmldom.sourceforge.net/

/** 
 * LinkedIn-O-Matic extracts info from a LinkedIn user's
 * public profile page via scrapping.
*/
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

	/** 
	 * Constructor
	 *
	 * @param string URL to a LinkedIn user's public profile 
	*/
    function __construct($targetUrl)
    {
        $this->targetUrl = $targetUrl;
        $this->html = file_get_html((string) $targetUrl);

        if () {
        	$linkedInVersion = 2;	
        } else {
        	$linkedInVersion = 1;
        }	
			// HTML selection queries
			// Must be updated as LinkedIn changes their markup
			// Basics
			$this->NAME_QUERY 			= 'span.full-name';
			$this->LOCALITY_QUERY 		= 'span.locality';
			$this->INDUSTRY_QUERY 		= 'dd.industry';
			$this->HEADLINE_QUERY 		= 'p.title';
			// Education
			$this->SCHOOL_QUERY			= 'h4.summary';
			// Links
			$this->LINKS_QUERY 			= 'div.profile-overview-content li a';
			// Picture
			$this->PICTURE_QUERY		= 'div.profile-picture img';
			// Education
			$this->DESCRIPTIONS_QUERY 	= 'p.description';
			$this->TITLES_QUERY 		= 'div.background-experience header h4';
			$this->ORGANIZATIONS_QUERY 	= 'div.background-experience header h5';
			$this->LOCATIONS_QUERY 		= 'div.background-experience span.locality';
			$this->DURATIONS_QUERY 		= 'span.experience-date-locale time';
    }	

	/** 
	 * Scrapes the header level information about a user
	 *
	 * @return array
	*/
    public function scrapeBasics () {

		$name 		 	= $this->checkElement($this->html->find(self::NAME_QUERY), array('flatten', 'text', 'clean'));
		$locality 		= $this->checkElement($this->html->find(self::LOCALITY_QUERY), array('flatten', 'text', 'clean'));
		$industry 		= $this->checkElement($this->html->find(self::INDUSTRY_QUERY), array('flatten', 'text', 'clean'));
		$headline 	 	= $this->checkElement($this->html->find(self::HEADLINE_QUERY), array('flatten', 'text', 'clean'));

		$basics = array(
			'name' 		=> $name,
			'locality' 	=> $locality,
			'industry' 	=> $industry,
			'headline' 	=> $headline,
		);

		return $basics;
    }

	/** 
	 * Scrapes the user's education history
	 *
	 * @return array
	*/
    public function scrapeEducation () {

		$school = $this->checkElement($this->html->find(self::SCHOOL_QUERY), array('flatten', 'text', 'clean'));   	

		$education = array(
			'school' => $school,
		);

		return $education;
    }

	/** 
	 * Scrapes the links the user has provided to their personal website etc
	 *
	 * @todo: find a better way to do this
	 * @return array
	*/
    public function scrapeLinks () {

		$webUrls 	= $this->checkElement($this->html->find(self::LINKS_QUERY));

		$websiteUrls = array();
		if ($webUrls) {
			foreach ($webUrls as $webUrl) {
				$url 	= $this->checkElement($webUrl, array('href', 'clean'));
				$start 	= strpos($url, '='); // Must be the beginning of the url piece of the LinkedIn url
				$end 	= strpos($url, '&url'); // Must be the last character following the url 
				$length = $end - $start;

				$type = trim(strip_tags($webUrl->plaintext));
				if (in_array($type, array('Personal Website', 'Company Website'))) {
					$websiteUrls[] = array(
						'type' => $type,
						'url' => substr($url, $start + 1, $length - 1),
					);
				}
			}
		}

		return $websiteUrls;
	}

	/** 
	 * Scrapes the path to the user's profile photo
	 *
	 * @return array
	*/
	public function scrapePicture () {

		$url = $this->checkElement($this->html->find(self::PICTURE_QUERY), array('flatten', 'source', 'clean'));   

		$picture = array(
			'url' => $url,
		);

		return $picture;
	}

	/** 
	 * Scrapes the links the user's employment history
	 *
	 * @return array
	*/
	public function scrapeEmployment () {

		$jobs 			= array();
		$description 	= $this->checkElement($this->html->find(self::DESCRIPTIONS_QUERY), array('flatten', 'text', 'clean'));
		$titles 		= $this->checkElement($this->html->find(self::TITLES_QUERY));
		$organizations 	= $this->checkElement($this->html->find(self::ORGANIZATIONS_QUERY));
		$locations	 	= $this->checkElement($this->html->find(self::LOCATIONS_QUERY));
		$durations	 	= $this->checkElement($this->html->find(self::DURATIONS_QUERY));
		if ($titles) {
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
				$n = $n + 3; // As of 06/29/14 LinkedIn isn't providing unique date identifiers
			}
		}

		return $jobs;
	}

	/** 
	 * Wrapper method for all the available scrapping methods
	 *
	 * @todo Experimental and untested...
	 * @return array
	*/
	public function scrapeAll () {

		$profile = array();

		$profile[] = array('basics' 		=> $this->scrapeBasics());
		$profile[] = array('education' 		=> $this->scrapeEducation());
		$profile[] = array('links' 			=> $this->scrapeLinks());
		$profile[] = array('picture' 		=> $this->scrapePicture());
		$profile[] = array('employment' 	=> $this->scrapeEmployment());

		return $profile;
	}

	/** 
	 * Check if the HTML query exists and optionally convert it into desired format
	 *
	 * @todo Experimental and untested...
	 * @return string
	*/
	public function checkElement ($element, $params = array()) {

		if (isset($element) && $element) {		
			if (in_array('flatten', $params)) {
				if (is_array($element) && isset($element[0])) {
					$element = $element[0];
				} else {
					return '';
				}
			}

			if (in_array('text', $params)) {
				$element = $element->plaintext;
			} else if (in_array('source', $params)) {
				$element = $element->src;
			} else if (in_array('href', $params)) {
				$element = $element->href;
			}

			if (in_array('clean', $params)) {
				$element = trim(strip_tags($element));
			}
			return $element;
		} else {
			return '';
		}
	}
}