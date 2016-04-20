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
    
}
