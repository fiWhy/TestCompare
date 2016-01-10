<?php
namespace App\Modules\VersionControl\Parsers;

class ParsersAdapter{
    
    
    /**
    * Chosen parser to work with text
    * @protected
    * @property {Object} App\Modules\VersionControl\Parsers\ParsersList\{Object}
    */
    protected $parser;
    
    /**
    * Array of chars that must be checked
    * @protected
    * @property array
    */
    protected $_independentChars;
    
    public function __construct(array $independentChars = [])
    {
        $this->_independentChars = $independentChars;
    }
    
    public function setParser($parser){
        $this->parser = $parser;
        $this->parser->_independentChars = $this->_independentChars;
    }
    
     /**
    * Function that realise compare
    * @protected
    * @method function
    * @param array $bodies
    * @return array description
    */
    public function compareImplementation(array $bodies, $cmpBody = null){
        return $this->parser->compareImplementation($bodies, $cmpBody = null);
    }
    
    /**
    * Description for function
    * @protected
    * @method function
    * @param array $lines
    * @return array
    */
    public function inLineCompareImplementation($lines){
        return $this->parser->inLineCompareImplementation($lines);    
    }
    
    /**
    * Check diffs by word
    * @protected
    * @method function
    * @param array $arr
    * @return return array
    */
    public function checkExplodedInline($arr){
     return $this->parser->checkExplodedInline($arr);   
    }
}