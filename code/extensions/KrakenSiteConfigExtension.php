<?php
/**
 * extends the @see SiteConfig class
 * Sets Kraken configuration
 *
 * @author Ryan
 */
class KrakenSiteConfigExtension extends DataExtension {
    
    private static $db = array(
        'KrakenAPIKey'=>'Varchar(255)',
        'KrakenAPISecret'=>'Varchar(255)',
        'DisableKraken'=>'Boolean'
    );
    
    public function updateCMSFields(FieldList $fields) {
        //Kraken tab
        $fields->addFieldsToTab('Root.Kraken',array(
            TextField::create('KrakenAPIKey', _t('Kraken.API_KEY', '_Kraken API Key'), null, 255),
            TextField::create('KrakenAPISecret', _t('Kraken.API_SECRET', '_Kraken API Secret'), null, 255),
            FieldGroup::create(
                    LiteralField::create('status', $this->getAPIStatusString())
                    )->setTitle(_t('Kraken.API_STATUS', '_API Status')
            ),
            CheckboxField::create('DisableKraken', _t('Kraken.DISABLE_KRAKEN', '_Disable Kraken on new uploads'))
        ));
    }
    
    /**
     * return a string stating the api status
     * @return String
     */
    public function getAPIStatusString(){
        $kraken = new KrakenService();
        
        if($kraken->getAPIStatus()){
            return _t('Kraken.API_OKAY', '_Your credentials are valid');
        }else {
            return _t('Kraken.API_PROBLEM', '_There is a problem with your credentials');
        }
    }
    
}
