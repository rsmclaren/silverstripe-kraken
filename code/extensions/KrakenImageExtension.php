<?php

/**
 * extends the @see Image class
 *
 * @author Ryan
 */
class KrakenImageExtension extends DataExtension {

	/**	
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		//js requirements
		Requirements::javascript(KRAKEN_BASE . '/js/AssetOptimizeImages.js');

		$fields->addFieldToTab('Root.Main', LiteralField::create(
						'Optimize', '<a '
						. 'href="admin/assets/optimizeImage?ID=' . $this->owner->ID . '"'
						. 'data-id="' . $this->owner->ID . '"'
						. 'class="action action-detail ss-ui-button ss-ui-button-ajax ui-button ui-widget ui-state-default ui-corner-all action_Optimize">' . _t('Kraken.OPTIMIZE', '_Optimize') . '</a>')
		);
	}

	public function getResizedImages() {
		$generatedImages = array();
		$cachedFiles = array();

		$folder = $this->owner->ParentID ? $this->owner->Parent()->Filename : ASSETS_DIR . '/';
		$cacheDir = Director::getAbsFile($folder . '_resampled/');

		if (is_dir($cacheDir)) {
			if ($handle = opendir($cacheDir)) {
				while (($file = readdir($handle)) !== false) {
					// ignore all entries starting with a dot
					if (substr($file, 0, 1) != '.' && is_file($cacheDir . $file)) {
						$cachedFiles[] = $file;
					}
				}
				closedir($handle);
			}
		}

		$pattern = $this->owner->getImageFilenamePatterns($this->owner->Name);

		foreach ($cachedFiles as $cfile) {
			if (preg_match($pattern['FullPattern'], $cfile, $matches)) {
				if (Director::fileExists($cacheDir . $cfile)) {
					$subFilename = substr($cfile, 0, -1 * strlen($this->owner->Name));
					preg_match_all($pattern['GeneratorPattern'], $subFilename, $subMatches, PREG_SET_ORDER);

					$generatorArray = array();
					foreach ($subMatches as $singleMatch) {
						$generatorArray[] = array('Generator' => $singleMatch['Generator'],
							'Args' => $this->owner->base64url_decode($singleMatch['Args']));
					}

					// Using array_reverse is important, as a cached image will
					// have the generators settings in the filename in reversed
					// order: the last generator given in the filename is the
					// first that was used. Later resizements are prepended
					$generatedImages[] = array('FileName' => $cacheDir . $cfile,
						'Generators' => array_reverse($generatorArray));
				}
			}
		}

		return $generatedImages;
	}

	public function getImageFilenamePatterns($filename) {
		$methodNames = $this->owner->allMethodNames(true);
		$generateFuncs = array();
		foreach ($methodNames as $methodName) {
			if (substr($methodName, 0, 8) == 'generate') {
				$format = substr($methodName, 8);
				$generateFuncs[] = preg_quote($format);
			}
		}
		// All generate functions may appear any number of times in the image cache name.
		$generateFuncs = implode('|', $generateFuncs);
		$base64url_match = "[a-zA-Z0-9_~]*={0,2}";
		return array(
			'FullPattern' => "/^((?P<Generator>{$generateFuncs})(?P<Args>" . $base64url_match . ")\-)+"
			. preg_quote($filename) . "$/i",
			'GeneratorPattern' => "/(?P<Generator>{$generateFuncs})(?P<Args>" . $base64url_match . ")\-/i"
		);
	}

	public static function base64url_decode($val) {
		return json_decode(
				base64_decode(str_pad(strtr($val, '~_', '+/'), strlen($val) % 4, '=', STR_PAD_RIGHT)), true
		);
	}

}
