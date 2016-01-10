<?php
namespace App\Modules\VersionControl\Parsers\ParsersList;

class TxtParser implements ParsersInterface{
    
    const DELETED_STRING = '<span class="c-deleted">%s</span>';
    const CHANGED_STRING = '<span class="c-changed">%s</span>';
    const ADDED_STRING   = '<span class="c-added">%s</span>';
    const TOUCHED_LINE   = '<div class="l-touched">%s</div>';
    
    const NEW_LINE       = '<div class="new-line">%s</div>';
    
    public $_independentChars;
    
    /**
    * Line that has been exploded
    * @protected
    * @property array
    */
    protected $_explodedLine;
    
    /**
    * Result of comparing
    * @protected
    * @property array
    */
    protected $_compareResult;
    
     /**
    * Function that realise compare
    * @protected
    * @method function
    * @param array $bodies
    * @return array description
    */
    public function compareImplementation(array $bodies, $cmpBody = null, $ready = false){ 
        $explodedBodies = [];
        
            /* Differences array */
            $this->_soArr = [[], []];
        
        if($cmpBody){
            $explodedBodies = $bodies;
            for($i = 0; $i < count($bodies); $i++){
                $compareText = $explodedBodies[$i];
                $arr = [$cmpBody, $compareText];
                
                
                
        $maxCount = 0;
        for($r = 0; $r < count($arr); $r++){
           if(count($arr[$r]) > $maxCount){
               $maxCount = count($arr[$r]);
               $cLine = $r;
           }
        }
                
               for($j = 0; $j < $maxCount; $j++){
                    $currentLine = $j;
                    if($this->checkFullLinePresent($arr, $currentLine))
                        $this->inLineCompareImplementation([$cmpBody[$currentLine], $compareText[$currentLine]]);
                } 
            }
            return true;
        }else{
            for($i = 0; $i < count($bodies); $i++){
                if(!$i){
                    $cmpBody = preg_split('/\\r\\n?|\\n/', $bodies[$i]);
                    continue;
                }
                $explodedBodies[] = preg_split('/\\r\\n?|\\n/', $bodies[$i]);
            }
            
            $ready = $this->compareImplementation($explodedBodies, $cmpBody, false);
        }
        
        if($ready)
            return $this->_soArr;
    }
    
    /**
    * Description for function
    * @protected
    * @method function
    * @param array $lines
    * @return array
    */
    public function inLineCompareImplementation($lines){
            $newArr = [];
            for($i = 0; $i < count($lines); $i++){
                $currentLine = $lines[$i];
                $newArr[$i] = [];
                $plus = true;
                for($c = 0, $array_index = 0; $c < strlen($lines[$i]); $c++, $plus?$array_index++:null){
                    $currentChar = $lines[$i][$c];
                    
                   switch($currentChar){
                        case in_array($currentChar, $this->_independentChars):
                           $newArr[$i][$array_index] = $currentChar;
                           break;
                       default:
                           if(!isset($newArr[$i][$array_index])){
                               $newArr[$i][$array_index] = $currentChar;
                           }else{
                               $newArr[$i][$array_index] .=  $currentChar;
                           }
                           $plus = $this->checkChar($currentChar, $currentLine, $c+1);
                           break;
                   };
                }
            }
        
            $this->checkExplodedInline($newArr);
        
        
        return $this->_compareResult;
    }
    
    /**
    * Check diffs by word
    * @protected
    * @method function
    * @param array $arr
    * @return return array
    */
    public function checkExplodedInline($arr){
        $maxCount = 0;
        for($i = 0; $i < count($arr); $i++){
           if(count($arr[$i]) > $maxCount){
               $maxCount = count($arr[$i]);
               $cLine = $i;
           }
        }
            $changes = false;
            for($i = 0, $fWord = 0, $sWord = 0; $i < $maxCount; $i++){
                
                $fWord = $this->getNearestInLine($fWord, $arr[0]);
                $sWord = $this->getNearestInLine($sWord, $arr[1]);
                
                if(!$fWord && $fWord !== 0){
                        if($sWord || $sWord == 0){
                            $arr[1][$sWord] = $this->touchLine(null, null, [self::ADDED_STRING], $arr[1][$sWord]);
                            $changes = true;
                        }
                }elseif(($fWord || $fWord == 0) && (!$sWord && $sWord !== 0)){
                    $arr[0][$fWord] = $this->touchLine(null, null, [self::DELETED_STRING], $arr[0][$fWord]);
                            $changes = true;
                }else{
                    if($arr[0][$fWord]!=$arr[1][$sWord]){
                        $arr[0][$fWord] = $this->touchLine(null, null, [self::CHANGED_STRING], $arr[0][$fWord]);
                        $arr[1][$sWord] = $this->touchLine(null, null, [self::CHANGED_STRING], $arr[1][$sWord]);
                            $changes = true;
                    }
                }
                
                if($fWord  || $fWord === 0){
                    $fWord++;
                }
                if($sWord || $sWord === 0)
                    $sWord++;
                   
            }
            
            $this->_soArr[0][] = [$this->convertPreparedLine($arr[0], $changes), true];
            $this->_soArr[1][] = [$this->convertPreparedLine($arr[1], $changes), true];
    }
    
    protected function checkFullLinePresent($arr, $line){
        $result = false;
        
        if(isset($arr[0][$line]))
            $fW = $this->getNearestInline(0, $arr[0][$line], true);
        
        if(isset($arr[1][$line]))
            $sW = $this->getNearestInline(1, $arr[1][$line], true);
        
        if(!isset($arr[0][$line])){
            if($sW || $sW === 0){
            $this->_soArr[1][] = [$this->touchLine(null, null, [self::ADDED_STRING, self::TOUCHED_LINE, self::NEW_LINE], $arr[1][$line]) , false];
            }
        }elseif(!isset($arr[1][$line])){
            if($fW || $fW === 0){
                $this->_soArr[0][] = [$this->touchLine(null, null, [self::DELETED_STRING, self::TOUCHED_LINE], $arr[0][$line]), true];
                $this->_soArr[1][] = [$this->touchLine(null, null, [self::TOUCHED_LINE], ''), false];
            }
        }else
            $result = true;
        
        return $result;
    }
    
   /**
    * Make a formatted string from array
    * @protected
    * @method function
    * @param array $phrases
    * @param bool $isChanged
    * @return string
    */
    protected function convertPreparedLine($phrases, $isChanged){
        if(!$isChanged)
            return implode('', $pharses);
        
        return $this->touchLine(null, null, [self::TOUCHED_LINE], implode($phrases));
    }
    
    /**
    * Check next char of line
    * @private
    * @method function
    * @param string $currentChar
    * @param string $string
    * @param int $index
    * @return boolean
    */
    private function checkChar($currentChar, $string, $index)
    {
        if(!isset($string[$index]))
            return false;
        $checkString = $string[$index];
        if($currentChar == $checkString || (!in_array($string[$index], $this->_independentChars) && $currentChar != ' ' && $checkString != ' ')){
                return false;
        }
            return true;
    }
    
    
     /**
    * Modify line and fields
    * @protected
    * @method function
    * @param int $lineNumber
    * @param int $fNumber = null
    * @param array $modificators
    * @param array|string $lines
    * @return void
    */
    protected function touchLine($lineNumber = null, $fNumber = null, array $modifiers, $lines)
    {
        $soLine;
        for($i = 0; $i < count($modifiers); $i++){
            if(is_null($fNumber) && $lineNumber){
                $result = $lines[$lineNumber] = sprintf($modifiers[$i], $lines[$lineNumber]);
            }elseif(is_null($lineNumber) && is_null($fNumber)){
                $result = $lines = sprintf($modifiers[$i], $lines);
            }else{
                $result = $lines[$lineNumber][$fNumber] = sprintf($modifiers[$i], $lines[$lineNumber][$fNumber]);
            }
        }
        
        return $result;
    }
    
    
    
    
    /**
    * Get nearest char that not is null
    * @protected
    * @method function
    * @param int $from
    * @param array $lineArr
    * @return int|false
    */
    protected function getNearestInLine($from, $lineArr, $isString = false)
    {
        $count = $isString?strlen($lineArr):count($lineArr);
        for($i = $from; $i < $count; $i++){
            if(!isset($lineArr[$i]))
                return false;
            if(trim($lineArr[$i]) != '')
                return $i;
        }
        
        return false;
    }
}