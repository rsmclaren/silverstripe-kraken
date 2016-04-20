<?php
/**
 * extends the @see File class
 *
 * @author Ryan
 */
class KrakenFileExtension extends DataExtension {
    
    private static $db=array(
        'Kraked'=>'Boolean',
        'KrakenID'=>'Varchar(255)'
    );
    
    /**
     * return a yes/no for the
     * Kraked boolean
     * @return string
     */
    public function getKrakedNice(){
        if($this->owner->Kraked === 1){
            return 'Yes';
        }else {
            return 'No';
        }
    }
    
}
