<?php

/**
* A PHP wrapper for the Google Maps Geocoding API v3.
*
* @author    Justin Stayton
* @copyright Copyright 2015 by Justin Stayton
* @license   https://github.com/jstayton/Miner/blob/master/LICENSE-MIT MIT
* @link      https://developers.google.com/maps/documentation/geocoding/intro
* @package   GoogleMapsGeocoder
* @version   2.4.0
*/
class GoogleMapsGeocoder{

    /**
    * HTTPS URL of the Google Geocoding API.
    */
    const URL = "https://maps.googleapis.com/maps/api/geocode/";

    /**
    * JSON response format.
    */
    const FORMAT_JSON = "json";

    /**
    * XML response format.
    */
    const FORMAT_XML = "xml";

    /**
    * Helps calculate a more realistic bounding box by taking into account the
    * curvature of the earth's surface.
    */
    const EQUATOR_LAT_DEGREE_IN_MILES = 69.172;

    /**
    * Response format.
    * @var string
    */
    private $format;

    /**
    * Address to geocode.
    * @var string
    */
    private $address;

    /**
    * Latitude to reverse geocode to the closest address.
    * @var float|string
    */
    private $latitude;

    /**
    * Longitude to reverse geocode to the closest address.
    * @var float|string
    */
    private $longitude;

    /**
    * Southwest latitude of the bounding box within which to bias geocode results.
    * @var float|string
    */
    private $boundsSouthwestLatitude;

    /**
    * Southwest longitude of the bounding box within which to bias geocode results.
    * @var float|string
    */
    private $boundsSouthwestLongitude;

    /**
    * Northeast latitude of the bounding box within which to bias geocode results.
    * @var float|string
    */
    private $boundsNortheastLatitude;

    /**
    * Northeast longitude of the bounding box within which to bias geocode results.
    * @var float|string
    */
    private $boundsNortheastLongitude;

    /**
    * Two-character, top-level domain (ccTLD) within which to bias Geocode results.
    * @var string
    */
    private $region;

    /**
    * Language code in which to return results.
    * @var string
    */
    private $language;

    /**
    * Address type(s) to restrict results to.
    * @var array
    */
    private $resultType = array();

    /**
    * Location type(s) to restrict results to.
    * @var array
    */
    private $locationType = array();

    /**
    * API key to authenticate with.
    * @var string
    */
    private $apiKey;

    /**
    * Constructor. The request is not executed until `geocode()` is called.
    * @param  string $address optional address to geocode
    * @param  string $format optional response format (JSON default)
    * @return GoogleMapsGeocoder
    */
    public function __construct($address = null, $format = self::FORMAT_JSON){
        $this->setAddress($address)
        ->setFormat($format);
    }

    /**
    * Set the response format.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#GeocodingResponses
    * @param  string $format response format
    * @return GoogleMapsGeocoder
    */
    public function setFormat($format){
        $this->format = $format;
        return $this;
    }

    /**
    * Get the response format.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#GeocodingResponses
    * @return string response format
    */
    public function getFormat(){
        return $this->format;
    }

    /**
    * Whether the response format is JSON.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#JSON
    * @return bool whether JSON
    */
    public function isFormatJson(){
        return $this->getFormat() == self::FORMAT_JSON;
    }

    /**
    * Whether the response format is XML.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#XML
    * @return bool whether XML
    */
    public function isFormatXml(){
        return $this->getFormat() == self::FORMAT_XML;
    }

    /**
    * Set the address to geocode.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#geocoding
    * @param  string $address address to geocode
    * @return GoogleMapsGeocoder
    */
    public function setAddress($address){
        $this->address = $address;
        return $this;
    }

    /**
    * Get the address to geocode.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#geocoding
    * @return string
    */
    public function getAddress(){
        return $this->address;
    }

    /**
    * Set the latitude/longitude to reverse geocode to the closest address.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
    * @param  float|string $latitude latitude to reverse geocode
    * @param  float|string $longitude longitude to reverse geocode
    * @return GoogleMapsGeocoder
    */
    public function setLatitudeLongitude($latitude, $longitude){
        $this->setLatitude($latitude)
        ->setLongitude($longitude);
        return $this;
    }

    /**
    * Get the latitude/longitude to reverse geocode to the closest address in comma-separated format.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
    * @return string|false comma-separated coordinates, or false if not set
    */
    public function getLatitudeLongitude(){
        $latitude = $this->getLatitude();
        $longitude = $this->getLongitude();

        if($latitude && $longitude){
            return $latitude . "," . $longitude;
        }
        return false;
    }

    /**
    * Set the latitude to reverse geocode to the closest address.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
    * @param  float|string $latitude latitude to reverse geocode
    * @return GoogleMapsGeocoder
    */
    public function setLatitude($latitude){
        $this->latitude = $latitude;
        return $this;
    }

    /**
    * Get the latitude to reverse geocode to the closest address.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
    * @return float|string latitude to reverse geocode
    */
    public function getLatitude(){
        return $this->latitude;
    }

    /**
    * Set the longitude to reverse geocode to the closest address.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
    * @param  float|string $longitude longitude to reverse geocode
    * @return GoogleMapsGeocoder
    */
    public function setLongitude($longitude){
        $this->longitude = $longitude;
        return $this;
    }

    /**
    * Get the longitude to reverse geocode to the closest address.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#ReverseGeocoding
    * @return float|string longitude to reverse geocode
    */
    public function getLongitude(){
        return $this->longitude;
    }

    /**
    * Set the bounding box coordinates within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @param  float|string $southwestLatitude southwest latitude boundary
    * @param  float|string $southwestLongitude southwest longitude boundary
    * @param  float|string $northeastLatitude northeast latitude boundary
    * @param  float|string $northeastLongitude northeast longitude boundary
    * @return GoogleMapsGeocoder
    */
    public function setBounds($southwestLatitude, $southwestLongitude, $northeastLatitude, $northeastLongitude){
        $this->setBoundsSouthwest($southwestLatitude, $southwestLongitude)->setBoundsNortheast($northeastLatitude, $northeastLongitude);

        return $this;
    }

    /**
    * Get the bounding box coordinates within which to bias geocode results in comma-separated, pipe-delimited format.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @return string|false comma-separated, pipe-delimited coordinates, or
    *                      false if not set
    */
    public function getBounds(){
        $southwest = $this->getBoundsSouthwest();
        $northeast = $this->getBoundsNortheast();

        if($southwest && $northeast){
            return $southwest . "|" . $northeast;
        }
        return false;
    }

    /**
    * Set the southwest coordinates of the bounding box within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @param  float|string $latitude southwest latitude boundary
    * @param  float|string $longitude southwest longitude boundary
    * @return GoogleMapsGeocoder
    */
    public function setBoundsSouthwest($latitude, $longitude){
        $this->boundsSouthwestLatitude = $latitude;
        $this->boundsSouthwestLongitude = $longitude;
        return $this;
    }

    /**
    * Get the southwest coordinates of the bounding box within which to bias geocode results in comma-separated format.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @return string|false comma-separated coordinates, or false if not set
    */
    public function getBoundsSouthwest(){
        $latitude = $this->getBoundsSouthwestLatitude();
        $longitude = $this->getBoundsSouthwestLongitude();

        if($latitude && $longitude){
            return $latitude . "," . $longitude;
        }
        return false;
    }

    /**
    * Get the southwest latitude of the bounding box within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @return float|string southwest latitude boundary
    */
    public function getBoundsSouthwestLatitude(){
        return $this->boundsSouthwestLatitude;
    }

    /**
    * Get the southwest longitude of the bounding box within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @return float|string southwest longitude boundary
    */
    public function getBoundsSouthwestLongitude(){
        return $this->boundsSouthwestLongitude;
    }

    /**
    * Set the northeast coordinates of the bounding box within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @param  float|string $latitude northeast latitude boundary
    * @param  float|string $longitude northeast longitude boundary
    * @return GoogleMapsGeocoder
    */
    public function setBoundsNortheast($latitude, $longitude){
        $this->boundsNortheastLatitude = $latitude;
        $this->boundsNortheastLongitude = $longitude;
        return $this;
    }

    /**
    * Get the northeast coordinates of the bounding box within which to bias geocode results in comma-separated format.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @return string|false comma-separated coordinates, or false if not set
    */
    public function getBoundsNortheast(){
        $latitude = $this->getBoundsNortheastLatitude();
        $longitude = $this->getBoundsNortheastLongitude();

        if($latitude && $longitude){
            return $latitude . "," . $longitude;
        }
        return false;
    }

    /**
    * Get the northeast latitude of the bounding box within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @return float|string northeast latitude boundary
    */
    public function getBoundsNortheastLatitude(){
        return $this->boundsNortheastLatitude;
    }

    /**
    * Get the northeast longitude of the bounding box within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#Viewports
    * @return float|string northeast longitude boundary
    */
    public function getBoundsNortheastLongitude(){
        return $this->boundsNortheastLongitude;
    }

    /**
    * Set the two-character, top-level domain (ccTLD) within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#RegionCodes
    * @param  string $region two-character, top-level domain (ccTLD)
    * @return GoogleMapsGeocoder
    */
    public function setRegion($region){
        $this->region = $region;
        return $this;
    }

    /**
    * Get the two-character, top-level domain (ccTLD) within which to bias geocode results.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#RegionCodes
    * @return string two-character, top-level domain (ccTLD)
    */
    public function getRegion(){
        return $this->region;
    }

    /**
    * Set the language code in which to return results.
    * @link   https://developers.google.com/maps/faq#languagesupport
    * @param  string $language language code
    * @return GoogleMapsGeocoder
    */
    public function setLanguage($language){
        $this->language = $language;
        return $this;
    }

    /**
    * Get the language code in which to return results.
    * @link   https://developers.google.com/maps/faq#languagesupport
    * @return string language code
    */
    public function getLanguage(){
        return $this->language;
    }

    /**
    * Set the address type(s) to restrict results to.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#reverse-restricted
    * @param  string|array $resultType address type(s)
    * @return GoogleMapsGeocoder
    */
    public function setResultType($resultType){
        $this->resultType = is_array($resultType) ? $resultType : array($resultType);
        return $this;
    }

    /**
    * Get the address type(s) to restrict results to.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#reverse-restricted
    * @return array address type(s)
    */
    public function getResultType(){
        return $this->resultType;
    }

    /**
    * Get the address type(s) to restrict results to separated by a pipe (|).
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#reverse-restricted
    * @return string address type(s) separated by a pipe (|)
    */
    public function getResultTypeFormatted(){
        return implode('|', $this->getResultType());
    }

    /**
    * Set the location type(s) to restrict results to.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#reverse-restricted
    * @param  string|array $locationType location type(s)
    * @return GoogleMapsGeocoder
    */
    public function setLocationType($locationType){
        $this->locationType = is_array($locationType) ? $locationType : array($locationType);
        return $this;
    }

    /**
    * Get the location type(s) to restrict results to.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#reverse-restricted
    * @return array location type(s)
    */
    public function getLocationType(){
        return $this->locationType;
    }

    /**
    * Get the location type(s) to restrict results to separated by a pipe (|).
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#reverse-restricted
    * @return string location type(s) separated by a pipe (|)
    */
    public function getLocationTypeFormatted(){
        return implode('|', $this->getLocationType());
    }

    /**
    * Set the API key to authenticate with.
    * @link   https://developers.google.com/console/help/new/#UsingKeys
    * @param  string $apiKey API key
    * @return GoogleMapsGeocoder
    */
    public function setApiKey($apiKey){
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
    * Get the API key to authenticate with.
    * @link   https://developers.google.com/console/help/new/#UsingKeys
    * @return string API key
    */
    public function getApiKey(){
        return $this->apiKey;
    }

    /**
    * Build the query string with all set parameters of the geocode request.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#GeocodingRequests
    * @return string encoded query string of the geocode request
    */
    private function geocodeQueryString(){
        $queryString = array();

        // One of the following is required.
        $address = $this->getAddress();
        $latitudeLongitude = $this->getLatitudeLongitude();

        // If both are set for some reason, favor address to geocode.
        if($address){
        $queryString['address'] = $address;
        }
        elseif($latitudeLongitude){
        $queryString['latlng'] = $latitudeLongitude;
        }

        // Optional parameters.
        $queryString['bounds'] = $this->getBounds();
        $queryString['region'] = $this->getRegion();
        $queryString['language'] = $this->getLanguage();
        $queryString['result_type'] = $this->getResultTypeFormatted();
        $queryString['location_type'] = $this->getLocationTypeFormatted();

        // Remove any unset parameters.
        $queryString = array_filter($queryString);

        // The signature is added later using the path + query string.
        if($this->getApiKey()){
        $queryString['key'] = $this->getApiKey();
        }

        // Convert array to proper query string.
        return http_build_query($queryString);
    }

    /**
    * Build the URL (with query string) of the geocode request.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#GeocodingRequests
    * @return string URL of the geocode request
    */
    private function geocodeUrl(){
        return self::URL . $this->getFormat() . "?" . $this->geocodeQueryString();
    }

    /**
    * Execute the geocoding request. The return type is based on the requested format: associative array if JSON, SimpleXMLElement object if XML.
    * @link   https://developers.google.com/maps/documentation/geocoding/intro#GeocodingResponses
    * @param  bool $raw whether to return the raw (string) response
    * @return string|array|SimpleXMLElement response in requested format
    */
    public function geocode($raw = false){
        $response = $this->retrieveURLData($this->geocodeUrl());

        if($raw){
            return $response;
        }
        elseif($this->isFormatJson()){
            return json_decode($response, true);
        }
        elseif($this->isFormatXml()){
            return new SimpleXMLElement($response);
        }
        return $response;
    }
    
    /**
     * Retrieves the URL Data using curl if install or with a default of file_get_content()
     * @param string $url This should be the URL of the page that you wish to retrieve data for
     * @return string The raw data retrieved from the URL is returned
     */
    protected function retrieveURLData($url){
        if(function_exists('curl_version')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        else{
            return file_get_contents($url);
        }
    }

    /**
    * Computes a four point bounding box around the specified location. This can then be used to find all locations within an X-mile range of a central location. A bounding box is much easier and faster to compute than a bounding radius.
    * The returned array contains two keys: 'lat' and 'lon'. Each of these contains another array with two keys: 'max' and 'min'. Four points are returned in total.
    * @param  float|string $latitude to draw the bounding box around
    * @param  float|string $longitude to draw the bounding box around
    * @param  int|float|string $mileRange mile range around point
    * @return array 'lat' and 'lon' 'min' and 'max' points
    */
    public static function boundingBox($latitude, $longitude, $mileRange){
        $maxLatitude = $latitude + $mileRange / self::EQUATOR_LAT_DEGREE_IN_MILES;
        $minLatitude = $latitude - ($maxLatitude - $latitude);

        $maxLongitude = $longitude + $mileRange / (cos($minLatitude * M_PI / 180) * self::EQUATOR_LAT_DEGREE_IN_MILES);
        $minLongitude = $longitude - ($maxLongitude - $longitude);

        return array('lat' => array('max' => $maxLatitude, 'min' => $minLatitude), 'lon' => array('max' => $maxLongitude, 'min' => $minLongitude));
    }
}