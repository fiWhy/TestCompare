<?php
namespace App\Modules\VersionControl\Parsers\ParsersList;

interface ParsersInterface{
    public function compareImplementation(array $bodies, $cmpBody = null);
    public function inLineCompareImplementation($lines);
    public function checkExplodedInline($arr);
}