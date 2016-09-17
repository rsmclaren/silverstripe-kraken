<?php
/**
 * extends the @see Upload class
 *
 * @author Ryan
 */
class KrakenUploadExtension extends Extension {
    
    /**
     * update the uploaded image with an optimized kraked file
     * @param File $file
     * @param Array $tmpFile     
     * @TODO need to remake cropped images
     */
    public function onAfterLoad($file, $tmpFile){
        $siteConfig = SiteConfig::current_site_config();        
        
        if(!$siteConfig->DisableKraken && $file->appCategory() === 'image'){            
            $krakenService = new KrakenService();

            $data = $krakenService->optimizeImage($file->getFullPath());

            //check if optimization was success
            if($data['success']){
                //attempt to download the kraked file
                $krakedFile = $krakenService->getOptimizedImage($data['kraked_url']);

                //update the uploaded file
                file_put_contents( $file->getFullPath(), $krakedFile ); 
                
                $file->Kraked = true;
                $file->write();
            }
        }
    }        
    
}
