<?php
namespace gmap\model;

use gmap\bo\LatLng;
use n2n\util\StringUtils;
use n2n\io\IoException;

class GeocodingApiAccessor {
	const GOOGLE_GEOCODING_API_URL = "http://maps.googleapis.com/maps/api/geocode/json?";
	
	public static function getLatLngForAddress(string $address, string $key) {
		$url = self::GOOGLE_GEOCODING_API_URL . 'key=' . $key 
				. '&address=' . self::prepareUrlForGoogle($address) . '&sensor=false';
		$response = StringUtils::jsonDecode(self::getContentForUrl($url), true);
		// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
		if (!isset($response['status']) || $response['status'] != 'OK') {
			return null;
		}
		$geometry = $response['results'][0]['geometry'];
		$lat= $geometry['location']['lat'];
		$lng = $geometry['location']['lng'];
		
		$latLng = new LatLng($lat, $lng);
		
		return $latLng;
	}
	
	public static function getAddressForLatLng(LatLng $latLng, string $key) {
		$url = self::GOOGLE_GEOCODING_API_URL . 'key=' . $key 
				. 'latlng=' . $latLng->getLat() . ',' . $latLng->getLng() . '&sensor=false';
		$response = StringUtils::jsonDecode(self::getContentForUrl($url), true);
		
		// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
		if ($response['status'] != 'OK') {
			return null;
		}
		$address = $response['results'][0]['formatted_address'];
		return $address;
	}
	
	private static function getContentForUrl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		if (false === $response) {
			$error = curl_error($ch);
			$errNo = curl_errno($ch);
			curl_close($ch);
			throw new CurlOperationException($error . ' (' . $errNo . ')');
		}
		curl_close($ch);
		return $response;
	}
	
	private static function prepareUrlForGoogle($url) {
		return preg_replace('/\s/', '+', $url);
	}
}

class CurlOperationException extends IoException {
	
}