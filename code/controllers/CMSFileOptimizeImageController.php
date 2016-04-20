<?php
/**
 * controller for optimizing images already uploaded
 * to the CMS. Displays a progress bar, and a list about what files
 * were updated
 */
class CMSFileOptimizeImageController extends LeftAndMain {      

    private static $url_segment = 'assets/optimize';
    private static $url_priority = 60;
    private static $required_permission_codes = 'CMS_ACCESS_AssetAdmin';
    private static $menu_title = 'Files';
    private static $tree_class = 'Folder';
    
    /**
     * Custom currentPage() method to handle opening the 'root' folder
     */
    public function currentPage() {
        $id = $this->currentPageID();
        if($id && is_numeric($id) && $id > 0) {
                $folder = DataObject::get_by_id('Folder', $id);
                if($folder && $folder->exists()) {
                        return $folder;
                }
        }
        return new Folder();
    }

    /**
     * Return fake-ID "root" if no ID is found (needed to upload files into the root-folder)
     */
    public function currentPageID() {
        if(is_numeric($this->getRequest()->requestVar('ID')))	{
                return $this->getRequest()->requestVar('ID');
        } elseif (is_numeric($this->urlParams['ID'])) {
                return $this->urlParams['ID'];
        } elseif(Session::get("{$this->class}.currentPage")) {
                return Session::get("{$this->class}.currentPage");
        } else {
                return 0;
        }
    }

    /**
     * @param null $id Not used.
     * @param null $fields Not used.
     * @return Form
     * @todo what template is used here? AssetAdmin_UploadContent.ss doesn't seem to be used anymore
     */
    public function getEditForm($id = null, $fields = null) {
        //css requirements               
        Requirements::css(KRAKEN_BASE . '/css/AssetOptimizeImages.css');
        
        //js requirements        
        Requirements::javascript(KRAKEN_BASE . '/js/AssetOptimizeImages.js');            

        if($currentPageID = $this->currentPageID()){
            Session::set("{$this->class}.currentPage", $currentPageID);	
        }
        
        return $this->renderWith('CMSFileOptimizeImage_EditForm');
    }
    
    /**
     * @param bool $unlinked
     * @return ArrayList
     */
    public function Breadcrumbs($unlinked = false) {
        $items = parent::Breadcrumbs($unlinked);

        // The root element should explicitly point to the root node.
        $items[0]->Link = Controller::join_links(singleton('AssetAdmin')->Link('show'), 0);

        // Enforce linkage of hierarchy to AssetAdmin
        foreach($items as $item) {
                $baselink = $this->Link('show');
                if(strpos($item->Link, $baselink) !== false) {
                        $item->Link = str_replace($baselink, singleton('AssetAdmin')->Link('show'), $item->Link);
                }
        }

        $items->push(new ArrayData(array(
                'Title' => _t('Kraken.OPTIMIZE_IMAGES', '_Optimize Images'),
                'Link' => $this->Link()
        )));

        return $items;
    }
    
    /**
     * return the relative path to the current folder
     * @return String
     */
    public function getBackLink(){
        $folder = $this->currentPage();
        
        return Controller::join_links(singleton('AssetAdmin')->Link('show'), $folder ? $folder->ID : 0);
    }
}