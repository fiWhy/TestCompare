<?php
namespace App\Modules\VersionControl;
use Illuminate\Filesystem\Filesystem as File;
use App\Modules\VersionControl\Parsers\ParsersList\TxtParser;
use App\Modules\VersionControl\Parsers;




class VersionControl implements VersionControlInterface{
    /**
    * List of parsers
    * @const
    * @property int
    */
    const TXT_PARSER = 0;
    
    /**
    * Filesystem class
    * @protected
    * @property Illuminate\Filesystem\Filesystem
    */
    protected $fileDeeds;
    
    /**
    * Cache adapter to save file
    * @protected
    * @property {Object}
    */
    protected $_cacheAdapter;
    
    /**
    * Key for cache storage
    * @protected
    * @property string
    */
    protected $_key;
    
    
    /**
    * Array of chars that must be checked
    * @protected
    * @property array
    */
    protected $_independentChars;
    
    
    
    /**
    * Get adapter of parsers
    * @protected
    * @property App\Modules\VersionControl\Parsers\ParsesAdapter;
    */
    protected $adapter;
    
    
    /**
    * Array of current parsers
    * @protected
    * @property array
    */
    protected $parsers;
    
    /**
    * Get cache adapter and key to store files data
    * @public
    * @method function
    * @param {Object} $cacheAdapter
    * @param {string} $key
    * @return void
    */
    public function __construct($cacheAdapter, $key)
    {
        $this->_fileDeeds = new File;
        $this->_cacheAdapter = $cacheAdapter;
        $this->_key = $key;
        $this->_independentChars = [',', '.', ':', '!',
                                    '?', '-', ';', '@', 
                                    '#', '$', '%', '^',
                                    '&', '*', '(', ')',
                                    '_', '=', '~', '`'];
        
        $this->parsers = [
            new TxtParser
        ];
    }
    
    /**
    * Set chars that must be cheched from new line instantly
    * @public
    * @method function
    * @param array $independentChars
    * @return {Object} $this
    */
    public function setIndependentChars(array $independentChars)
    {
        $this->_independentChars = $independentChars;
        return $this;
    }
    
    /**
    * Add chars that must be cheched from new line instantly
    * @public
    * @method function
    * @param {Object} string|array $independentChar
    * @return {Object} $this
    */
    public function addIndependentChar($independentChar)
    {
        if(is_array($independentChar)){
            for($i = 0; $i < count($independentChar); $i++){
                     if(!in_array($independentChar[$i], $this->_independentChars))
                        $this->_independentChars[] = $independentChar[$i];
            }
        }else{
            if(!in_array($independentChar, $this->_independentChars))
                $this->_independentChars[] = $independentChar;
        }
        return $this;
    }
    
    /**
    * Set parser
    * @public
    * @method function
    * @param int $const
    * @return {Object} $this
    */
    public function setParser($const){
        if(is_object($const)){
            if(!($const instanceof ParsersAdapter))
                throw new Error('Parser must be instace of ParsersAdapter!');
            $this->adapter = $const;
        }else{
            $this->adapter = new Parsers\ParsersAdapter($this->_independentChars);
            $this->adapter->setParser($this->parsers[$const]);
        }
        
        return $this;
    }
    
    /**
    * Returns parser
    * @public
    * @method function
    * @return {Object} App\Modules\VersionControl\Parsers\ParsersAdapter
    */
    public function getParser(){
        return $this->adapter;
    }
    
    /**
    * Prepare files to compare
    * @public
    * @method function
    * @return {Object} $this
    */
    public function loadFiles($file, $expireMinutes = 1)
    {
        if(is_array($file))
            $this->loadLoop($file, 1, function($fileInner){
                $this->writeToCache($fileInner, $expireMinutes);
            });
        else{
            $this->writeToCache($this->_fileDeeds->get($file->getPathname()), $expireMinutes);  
        }   
    }
    
    /**
    * Save all data to cache for compare
    * @protected
    * @method function
    * @param string $body
    * @param int $expireMinutes
    * @return void
    */
    protected function writeToCache($body, $expireMinutes)
    {
        $c = $this->_cacheAdapter;
        $storage = $c::get($this->_key);
        if(empty($storage) || !$c::has($this->_key)){
            $body = [$body];
            $c::add($this->_key, $body, $expireMinutes);
        }else{
            $storage = $c::get($this->_key);
            $storage[] = $body;
            $c::put($this->_key, $storage, $expireMinutes);
        }
    }
    
   /**
    * Load files in loop if want to load pack of files
    * @protected
    * @method function
    * @param array $files
    * @param int $expireMinutes
    * @param function $callback
    * @return {Object} $this
    */
    protected function loadLoop($files, $expireMinutes, $callback)
    {
        foreach($files as $file){
            $callback($file->getPathname());
        }
        
        return $this;
    }
    
    /**
    * Compare files and return array with fields and changes
    * @public
    * @method function
    * @return {Object} array
    */
    public function compare($filesCount)
    {
        
        $c = $this->_cacheAdapter;
        $data = [];
        
        
        if($filesCount > 2){          
            $this->clearCache();
            return false;
        }
        
        if($c::has($this->_key)){
            $data = $c::get($this->_key);
            if(count($data) == $filesCount){             
                $this->clearCache();
                return $this->getParser()->compareImplementation($data);
            }
        }            
        
        return false;
    }
    
    public function clearCache()
    {
        $c = $this->_cacheAdapter;
        $c::forget($this->_key);
        return $this;
    }
    
}