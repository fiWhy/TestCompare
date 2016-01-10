<?php
namespace App\Modules\VersionControl;


interface VersionControlInterface{
    
    /**
    * Get cache adapter and key to store files data
    * @public
    * @method function
    * @param {string} $key
    * @return void
    */
    public function __construct($cacheAdapter, $key);
    
    /**
    * @public
    * @method function
    * @param {Object} $file
    * @param int $expireMinutes
    * @return {Object} self
    */
    public function loadFiles($file, $expireMinutes = 1);
    
    /**
    * @public
    * @method function
    * @param int $filesCount
    * @return {Object} self
    */
    public function compare($filesCount);
}