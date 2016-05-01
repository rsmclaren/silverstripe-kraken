<?php
/**
 * extends the @see AssetAdmin class
 * @author Ryan
 */
class KrakenAssetAdminExtension extends Extension {
    
    private static $allowed_actions=array(
        'optimizeImage',
        'imagesToOptimize' 
    );    
    
    public function updateEditForm($form){
        $folder = $this->owner->currentPage();
        
        $fields = $form->Fields();
        
        $optimizeImagesButton = LiteralField::create(
                'OptimizeImagesButton',
                sprintf(
                        '<a '
                        . 'class="ss-ui-button ss-ui-action ui-button-text-icon-primary cms-panel-link font-icon-eye cms-optimize-images-link" '                        
                        . 'href="%s">%s</a>',                        
                        Controller::join_links($this->owner->Link('optimize'), '?ID=' . $folder->ID),
                        _t('Kraken.OPTIMIZE_IMAGES', '_Optimize Images')
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
    public function optimizeImage(){                       
        $krakenService = new KrakenService();
        
        if($this->owner->request->getVar('ID') >= 1){
            $image = Image::get()->filter('Kraked', 0)->byID(intval($this->owner->request->getVar('ID')));
        }
        
        if($image){            
            $data = $krakenService->optimizeImage($image->getFullPath());
            
            //check if optimization was success
            if($data['success']){
                //attempt to download the kraked file
                $krakedFile = $krakenService->getOptimizedImage($data['kraked_url']);
                
                //get the images current size
                $unoptimizedSize = $image->getSize();

                //update the file
                file_put_contents( $image->getFullPath(), $krakedFile ); 

                $image->Kraked = true;
                $image->write();
                $image->deleteFormattedImages();
                
                if(Director::is_ajax()){
                    return json_encode(array(
                            'Name'=>$image->Name,
                            'UnoptimizedSize'=>$unoptimizedSize,
                            'OptimizedSize'=>$image->getSize()
                    ));
                 }else {
                    $message = _t('Kraken.OPTIMIZED', '_Optimized');

                    $this->owner->getResponse()->addHeader('X-Status', rawurlencode($message));
                     
                    return;
                }       
            }                                          
        }
    }
    
    /**
     * get the IDs of the images that have not 
     * been optimized
     * @return json
     */
    public function imagesToOptimize(){
        //only get images that haven't been optimized
        $images = Image::get()->filter('Kraked', 0);
        
        //if we're in a folder, only optimize the images in that folder
        if($this->owner->request->getVar('ParentID') >= 1){
            $images = $images->filter('ParentID', intval($this->owner->request->getVar('ParentID')));
        }
        
        if($images->count() >= 1){
            return json_encode($images->column('ID'));
        }else {
            return json_encode(false);
        }                
    }
    
}
