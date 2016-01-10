<?php

namespace App\Http\Controllers\Version;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\VersionControl\VersionControl;
use \Cache;

class VersionControlController extends Controller
{
    
    /**
    * @protected
    * @value App\Modules\VersionController\VersionControl;
    */
    protected $_vController;
    
   /**
    * @public
    * @method function
    * @return void
    */
    public function __construct()
    {
        $this->_vController = new VersionControl(new Cache(), 'file');
    }
    
    /**
    * Initiate page
    * @public
    * @method function
    * @return {Object} array
    */
    public function indexAction()
    {
        return view('compare');
    }
    
    /**
    * Take files to compare
    * @public
    * @method function
    * @return {Object} array
    */
    public function postAction(Request $request)
    {
        $response = [];
        $count = $request->input('count');
        
        if ($request->isMethod('post')) {
            $response['head'] = 200;
         $this->_vController->loadFiles($request->file('images'));
            $this->_vController->setParser(VersionControl::TXT_PARSER);
           $compare = $this->_vController->compare($count);
            
            if($compare)
                $response['body'] = $compare;
            else
                $response['body'] = null;
        }else{
            $response['head'] = 400;
        }
        
        return response()->json($response);
    }
    
}
