// <?php
// namespace gmap\rocket\modificator;

// use rocket\script\entity\modificator\impl\IndependentScriptModificatorAdapter;
// use rocket\script\core\SetupProcess;
// use rocket\script\entity\manage\mapping\ScriptSelectionMapping;
// use rocket\script\entity\manage\ScriptState;
// use n2n\dispatch\option\impl\OptionCollectionImpl;
// use rocket\script\entity\field\impl\string\StringScriptField;
// use n2n\dispatch\option\impl\EnumOption;
// use n2n\dispatch\option\impl\StringOption;
// use rocket\script\entity\manage\mapping\OnWriteMappingListener;
// use gmap\model\GeocodingApiAccessor;
// use rocket\script\entity\field\impl\numeric\DecimalScriptField;
// class LatLngModificator extends IndependentScriptModificatorAdapter {

// 	const PROP_NAME_REFERENCED_STREET_SCRIPT_FIELD_ID = 'street';
// 	const PROP_NAME_REFERENCED_CITY_SCRIPT_FIELD_ID = 'city';
// 	const PROP_NAME_REFERENCED_COUNTRY_SCRIPT_FIELD_ID = 'country';
// 	const PROP_NAME_DEFAULT_COUNTRY = 'default-country';

// 	const PROP_NAME_REFERENCED_LAT_SCRIPT_FIELD_ID = 'lat';
// 	const PROP_NAME_REFERENCED_LNG_SCRIPT_FIELD_ID = 'lng';
	
// 	/**
// 	 * @var StringScriptField
// 	 */
// 	private $streetScriptField;
// 	private $cityScriptField;
// 	private $countryScriptField;
// 	private $defaultCountry;
	
// 	private $latScriptField;
// 	private $lngScriptField;
	
// 	public function setup(SetupProcess $setupProcess) {
// 		$fieldCollection = $this->getEntityScript()->getFieldCollection();
		
// 		$streetScriptFieldId = $this->attributes->get(self::PROP_NAME_REFERENCED_STREET_SCRIPT_FIELD_ID);
// 		if ($fieldCollection->containsId($streetScriptFieldId)) {
// 			$this->streetScriptField = $fieldCollection->getById($streetScriptFieldId);
// 		}
		
// 		$cityScriptFieldId = $this->attributes->get(self::PROP_NAME_REFERENCED_CITY_SCRIPT_FIELD_ID);
// 		if ($fieldCollection->containsId($cityScriptFieldId)) {
// 			$this->cityScriptField = $fieldCollection->getById($cityScriptFieldId);
// 		}

// 		$countryScriptFieldId = $this->attributes->get(self::PROP_NAME_REFERENCED_COUNTRY_SCRIPT_FIELD_ID);
// 		if ($fieldCollection->containsId($countryScriptFieldId)) {
// 			$this->countryScriptField = $fieldCollection->getById($countryScriptFieldId);
// 		}
		
// 		$this->defaultCountry = $this->attributes->get(self::PROP_NAME_DEFAULT_COUNTRY);
		
// 		$latScriptFieldId = $this->attributes->get(self::PROP_NAME_REFERENCED_LAT_SCRIPT_FIELD_ID);
// 		if ($fieldCollection->containsId($latScriptFieldId)) {
// 			$this->latScriptField = $fieldCollection->getById($latScriptFieldId);
// 		}

// 		$lngScriptFieldId = $this->attributes->get(self::PROP_NAME_REFERENCED_LNG_SCRIPT_FIELD_ID);
// 		if ($fieldCollection->containsId($lngScriptFieldId)) {
// 			$this->lngScriptField = $fieldCollection->getById($lngScriptFieldId);
// 		}
// 	}
	
// 	public function setupScriptSelectionMapping(ScriptState $scriptState, ScriptSelectionMapping $ssm) {
// 		if (null === $this->streetScriptField || null === $this->cityScriptField || 
// 				null === $this->latScriptField || null === $this->lngScriptField) return;
// 		$that = $this;
// 		$ssm->registerListener(new OnWriteMappingListener(function() use ($that, $ssm) {
// 			$street = $ssm->getValue($that->streetScriptField->getId());
// 			$city = $ssm->getValue($that->cityScriptField->getId());
			
// 			if (!$street || !$city) return;
			
// 			$curLat = null;
// 			$curLng = null;
// 			$curStreet = null;
// 			$curCity = null;
// 			$curCountry = null;
// 			if (!$ssm->getScriptSelection()->isNew()) {
// 				$currentEntity = $ssm->getScriptSelection()->getOriginalEntity();
// 				$curStreet = $that->streetScriptField->getPropertyAccessProxy()->getValue($currentEntity);
// 				$curCity = $that->cityScriptField->getPropertyAccessProxy()->getValue($currentEntity);
// 				$curLat = $that->latScriptField->getPropertyAccessProxy()->getValue($currentEntity);
// 				$curLng = $that->lngScriptField->getPropertyAccessProxy()->getValue($currentEntity);
// 			}
			
// 			if ($curLat !== $ssm->getValue($that->latScriptField->getId()) ||
// 					$curLng !== $ssm->getValue($that->lngScriptField->getId()) ||
// 					($curStreet === $street && $curCity === $city)) {
// 				return;
// 			}
			
// 			$country = $this->defaultCountry;
// 			if (null !== $that->countryScriptField 
// 					&& null !== ($selectedCountry = $ssm->getValue($that->countryScriptField->getId()))) {
// 				$country = $selectedCountry;
// 			}
// 			$address = implode(' ', array_filter(array($street, $city, $country)));
// 			$latLng = GeocodingApiAccessor::getLatLngForAddress($address);
// 			if (null === $latLng) return;
// 			$ssm->setValue($that->latScriptField->getId(), $latLng->getLat());
// 			$ssm->setValue($that->lngScriptField->getId(), $latLng->getLng());
// 		}));
// 	}
	
// 	public function createOptionCollection() {
// 		$optionCollection = new OptionCollectionImpl();
// 		$optionCollection->addOption(self::PROP_NAME_REFERENCED_STREET_SCRIPT_FIELD_ID, 
// 				new EnumOption('Street', $this->determineStringOptions()));
// 		$optionCollection->addOption(self::PROP_NAME_REFERENCED_CITY_SCRIPT_FIELD_ID, 
// 				new EnumOption('City', $this->determineStringOptions()));
// 		$optionCollection->addOption(self::PROP_NAME_REFERENCED_COUNTRY_SCRIPT_FIELD_ID, 
// 				new EnumOption('Country', $this->determineStringOptions()));
// 		$optionCollection->addOption(self::PROP_NAME_DEFAULT_COUNTRY, 
// 				new StringOption('Default Country'));
// 		$optionCollection->addOption(self::PROP_NAME_REFERENCED_LAT_SCRIPT_FIELD_ID, 
// 				new EnumOption('Latitude', $this->determineDecimalOptions()));
// 		$optionCollection->addOption(self::PROP_NAME_REFERENCED_LNG_SCRIPT_FIELD_ID, 
// 				new EnumOption('Longitude', $this->determineDecimalOptions()));
// 		return $optionCollection;
// 	}
	
// 	private function determineStringOptions() {
// 		$options = array();
// 		foreach ($this->getEntityScript()->getFieldCollection() as $scriptField) {
// 			if (!$scriptField instanceof StringScriptField) continue;
// 			$options[$scriptField->getId()] = $scriptField->getLabel();
// 		}
// 		return $options;
// 	}
	
// 	private function determineDecimalOptions() {
// 		$options = array();
// 		foreach ($this->getEntityScript()->getFieldCollection() as $scriptField) {
// 			if (!$scriptField instanceof DecimalScriptField) continue;
// 			$options[$scriptField->getId()] = $scriptField->getLabel();
// 		}
// 		return $options;
// 	}
// }