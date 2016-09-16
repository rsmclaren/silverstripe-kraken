<?php

if (class_exists('ImagickBackend')) {

/**
 * after writing file, optimizes the image using
 * the Kraken service
 * 
 * @author Ryan
 */
class KrakenImagickBackend extends ImagickBackend {
 
    /**
     * Calls the original writeTo function and then optimizes the image
     * @param String $filename
     */
    public function writeTo($filename) {
        parent::writeTo($filename);       
        
        $siteConfig = SiteConfig::current_site_config();
        
        if(!$siteConfig->DisableKraken){
            $krakenService = new KrakenService();

            $data = $krakenService->optimizeImage($filename);

            //check if optimization was success
            if ($data['success'] && $data['saved_bytes'] >= 0) {

                //attempt to download the kraked file
                $krakedFile = $krakenService->getOptimizedImage($data['kraked_url']);

                //update the file
                if ($krakedFile) {                                                               
                    file_put_contents($filename, $krakedFile);
                }
            }        
        }
    }
    
}

}