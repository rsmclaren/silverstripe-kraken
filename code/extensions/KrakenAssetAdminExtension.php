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
	private static $supported_image = array(
		'gif',
		'jpg',
		'jpeg',
		'png'
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

		$image = $this->owner->request->getVar('image');

		//check if a file path was supplied
		if ($image) {
			$data = $krakenService->optimizeImage($image);
			
			//check if optimization was success
			if ($data['success'] && $data['saved_bytes'] >= 1) {
				
				//attempt to download the kraked file
				$krakedFile = $krakenService->getOptimizedImage($data['kraked_url']);

				//update the file
				file_put_contents($image, $krakedFile);
			}

			if (Director::is_ajax()) {
				return json_encode(array(
					'Name' => $data['file_name'],
					'UnoptimizedSize' => File::format_size($data['original_size']),
					'OptimizedSize' => File::format_size($data['kraked_size'])
				));
			} else {
				$message = _t('Kraken.OPTIMIZED', '_Optimized');

				$this->owner->getResponse()->addHeader('X-Status', rawurlencode($message));

				return;
			}
		}
	}

	/**
	 * Get the Filepaths of every image in the current folder
	 * @return json
	 */
	public function imagesToOptimize() {
		if ($this->owner->request->getVar('ParentID') >= 1) {
			$folder = Folder::get()->byID(intval($this->owner->request->getVar('ParentID')))->Filename;
		} else {
			$folder = ASSETS_PATH . '/';
		}

		$cacheDir = Director::getAbsFile($folder);

		if (is_dir($cacheDir)) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir));
			$paths = array();

			foreach ($files as $path => $file) {
				$ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
				if (in_array($ext, self::$supported_image)) {
					$paths[] = $path;
				}
			}
		}

		if (!empty($paths)) {
			return json_encode($paths);
		} else {
			return json_encode(false);
		}
	}

}
