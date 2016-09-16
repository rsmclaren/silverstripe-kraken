<?php

/**
 * extends the @see AssetAdmin class
 * @author Ryan
 */
class KrakenAssetAdminExtension extends Extension {

	private static $allowed_actions = array(
		'optimizeImage',
		'imagesToOptimize'
	);

	public function updateEditForm($form) {
		$folder = $this->owner->currentPage();

		$fields = $form->Fields();

		$optimizeImagesButton = LiteralField::create(
						'OptimizeImagesButton', sprintf(
								'<a '
								. 'class="ss-ui-button ss-ui-action ui-button-text-icon-primary cms-panel-link font-icon-eye cms-optimize-images-link" '
								. 'href="%s">%s</a>', Controller::join_links($this->owner->Link('optimize'), '?ID=' . $folder->ID), _t('Kraken.OPTIMIZE_IMAGES', '_Optimize Images')
						)
		);

		//add the optimize images button
		$fields->insertAfter($optimizeImagesButton, 'SyncButton');

		//update the grid field display fields
		$columns = $fields->fieldByName('Root.ListView.File')->getConfig()->getComponentByType('GridFieldDataColumns');
		$displayFields = $columns->getDisplayFields($fields->fieldByName('Root.ListView.File'));
		$displayFields['getKrakedNice'] = _t('Kraken.OPTIMIZED', '_Optimized');
		$columns->setDisplayFields($displayFields);
	}

	/**
	 * optimize a single image and return a
	 * json array
	 * @return json
	 */
	public function optimizeImage() {
		$krakenService = new KrakenService();
		$response = array();

		if ($this->owner->request->getVar('ID') >= 1) {
			$image = Image::get()->byID(intval($this->owner->request->getVar('ID')));
		}

		//check if a file path was supplied
		if ($image) {

			//optimize this image
			if (!$image->Kraked) {
				$data = $krakenService->optimizeImage($image->getFullPath());

				//check if optimization was success
				if ($data['success'] && $data['saved_bytes'] >= 0) {

					//attempt to download the kraked file
					$krakedFile = $krakenService->getOptimizedImage($data['kraked_url']);

					//update the file
					if ($krakedFile) {
						file_put_contents($image, $krakedFile);
						$image->Kraked = true;						
						$image->write();

						$response['Name'] = $data['file_name'];
						$response['UnoptimizedSize'] = File::format_size($data['original_size']);
						$response['OptimizedSize'] = File::format_size($data['kraked_size']);
					}
				}
			}

			$resizedImageCount = $this->optimizeFormattedImages($image);

			$response['FormattedImagesMessage'] = "Optimzed {$resizedImageCount} formatted images";

			if (Director::is_ajax()) {
				return json_encode($response);
			} else {
				$message = _t('Kraken.OPTIMIZED', '_Optimized');

				$this->owner->getResponse()->addHeader('X-Status', rawurlencode($message));

				return;
			}
		}
	}
	
	/**
	 * optimize formatted images
	 * @param Image $image
	 */
	public function optimizeFormattedImages(Image $image) {
		$krakenService = new KrakenService();
		
		$resizedImages = $image->getResizedImages();
		$resizedImageCount = 0;
		
		if ($resizedImages) {
			foreach ($resizedImages as $resizedImage) {				
				$data = $krakenService->optimizeImage($resizedImage['FileName']);

				//check if optimization was success
				if ($data['success'] && $data['saved_bytes'] >= 0) {
					//attempt to download the kraked file
					$krakedFile = $krakenService->getOptimizedImage($data['kraked_url']);

					//update the file
					if ($krakedFile) {
						file_put_contents($resizedImage['FileName'], $krakedFile);
						$resizedImageCount++;
					}
				}				
			}
		}
        
        return $resizedImageCount;
	}

	/**
	 * Get every non kraked image in the current folder
	 * @TODO this should get subfolders as well
	 * @return json
	 */
	public function imagesToOptimize() {
		$images = Image::get()->filter('Kraked', 0);

		if ($this->owner->request->getVar('ParentID') >= 1) {
			$images = $images->filter('ParentID', intval($this->owner->request->getVar('ParentID')));
		}

		if ($images->count() >= 1) {
			return json_encode($images->column('ID'));
		} else {
			return json_encode(false);
		}
	}

}
