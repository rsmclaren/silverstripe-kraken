<?php
/**
 * extends the @see Image class
 *
 * @author Ryan
 */
class KrakenImageExtension extends DataExtension {
    
    public function updateCMSFields(FieldList $fields) {
        //js requirements
        Requirements::javascript(KRAKEN_BASE . '/js/AssetOptimizeImages.js');
        
        //add kraked status
        $fields->insertAfter(
            ReadonlyField::create(
                    "Optimized", 
                    _t('Kraken.OPTIMIZED','_Optimized') . ':', $this->owner->getKrakedNice()),
            'BackLinkCount'
        );
        
        //add optimize image button
        if(!$this->owner->Kraked){
            $fields->addFieldToTab('Root.Main', LiteralField::create(
                    'Optimize', '<a '
                    . 'href="admin/assets/optimizeImage?ID=' .$this->owner->ID. '"'
                    . 'data-id="'.$this->owner->ID.'"'
                    . 'class="action action-detail ss-ui-button ss-ui-button-ajax ui-button ui-widget ui-state-default ui-corner-all action_Optimize">'._t('Kraken.OPTIMIZE', '_Optimize').'</a>')
            );
        }       
    }
	
	/**
	 * This is a copy of @Image::getGeneratedImages()
	 * return an array of formatted images
	 */
	public function getTheFormattedImages(){
		$generatedImages = array();
		$cachedFiles = array();

		$folder = $this->owner->ParentID ? $this->owner->Parent()->Filename : ASSETS_DIR . '/';
		$cacheDir = Director::getAbsFile($folder . '_resampled/');

		// Find all paths with the same filename as this Image (the path contains the transformation info)
		if(is_dir($cacheDir)) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir));
			foreach($files as $path => $file){
				if ($file->getFilename() == $this->owner->Name) {
					$cachedFiles[] = $path;
				}
			}
		}

		$pattern = $this->getTheFilenamePatterns($this->owner->Name);

		// Reconstruct the image transformation(s) from the format-folder(s) in the path
		// (if chained, they contain the transformations in the correct order)
		foreach($cachedFiles as $cf_path) {
			preg_match_all($pattern['GeneratorPattern'], $cf_path, $matches, PREG_SET_ORDER);

			$generatorArray = array();
			foreach ($matches as $singleMatch) {
				$generatorArray[] = array(
					'Generator' => $singleMatch['Generator'],
					'Args' => Convert::base64url_decode($singleMatch['Args'])
				);
			}

			$generatedImages[] = array(
				'FileName' => $cf_path,
				'Generators' => $generatorArray
			);
		}

		return $generatedImages;
	}
	
	/**
	 * This is a copy of @see Image::getFilenamePatterns
	 * Generate patterns that will help to match filenames of cached images
	 * @param string $filename Filename of source image
	 * @return array
	 */
	private function getTheFilenamePatterns($filename) {
		$methodNames = $this->owner->allMethodNames(true);
		$generateFuncs = array();
		foreach($methodNames as $methodName) {
			if(substr($methodName, 0, 8) == 'generate') {
				$format = substr($methodName, 8);
				$generateFuncs[] = preg_quote($format);
			}
		}
		// All generate functions may appear any number of times in the image cache name.
		$generateFuncs = implode('|', $generateFuncs);
		$base64url_match = "[a-zA-Z0-9_~]*={0,2}";
		return array(
				'FullPattern' => "/^((?P<Generator>{$generateFuncs})(?P<Args>" . $base64url_match . ")\/)+"
									. preg_quote($filename) . "$/i",
				'GeneratorPattern' => "/(?P<Generator>{$generateFuncs})(?P<Args>" . $base64url_match . ")\//i"
		);
	}
    
}
