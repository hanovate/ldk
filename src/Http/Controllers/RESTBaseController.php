<?php

namespace Unmit\ldk\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Unmit\ldk\Models\AbstractAPIModel;
use Unmit\ldk\Models\RESTable;

/**
 * Controller to consolidate the common actions that are related to a Model entity.  This controller
 * is designed for a one to one relation with a Model entity.  If a controller is needing to work with
 * multiple Model objects this class likely not be efficient.  Actions that will need to be implemented in the
 * extending class will be create() and custom queries.
 *
 * @package Unm
 * @author  rvestra
 * @version 1.0
 * @todo    this class appears to be abstract and more thought into having an interface
 */

class RESTBaseController extends Controller
{
    /**
     * The model name will set the context for the entity that CRUD will be applied to. 
     */
    protected $modelName;

    /**
     * The Model object that will likely be instantiated during the extended controller construction. 
     */
    protected $entityModel;

    /**
     * @return mixed
     */
    public function getEntityModel()
    {
        return $this->entityModel;
    }

    /**
     * @param mixed $entityModel
     */
    public function setEntityModel(RESTable $entityModel): void
    {
        $this->entityModel = $entityModel;
    }

    /**
     * An @array containing the list of selection arrays for use on the blade view drop down, multi-seledts, etc. 
     */
    protected $listOfValues = array();

    /**
     * An @array containing the request Validations; can be retrieved with accessor methods to apply to $entityModel 
     */
    protected $validations = array();

    /**
     * Allows the extending controller the ability to refer a string to use with Eloquent or DB Facade ->groupBy() 
     */
    protected $groupBy;

    /**
     * Allows the extending controller the ability to refer a string to use with Eloquent or DB Facade ->orderBy() 
     */
    protected $sortBy;

    /**
     * page count to be accessed by pagination  
     */
    protected $perPageCount;

    /**
     * @return integer
     */
    public function getPerPageCount()
    {
        if(is_null($this->perPageCount)) {
            $this->perPageCount = 50;
        }
        return $this->perPageCount;
    }

    /**
     * @param integer $perPageCount
     */
    public function setPerPageCount(int $perPageCount)
    {
        $this->perPageCount = $perPageCount;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param string $sortBy
     */
    public function setSortBy(string $sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return string
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * @param string $groupBy
     */
    public function setGroupBy(string $groupBy)
    {
        $this->groupBy = $groupBy;
    }

    /**
     * @return array
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     *
     * @param array $validations
     */
    public function setValidations($validations)
    {
        $this->validations = $validations;
    }

    /**
     * .
     *
     * @param  Request  $request
     * @return Response
     */
    public function isTokenFresh()
    {
        try
        {
            if (is_null(session('token')))
            {
                session(['token' => $this->entityModel->refreshAccessToken()]);
                //todo: check an expired secret status code and log or report
            }
        }
        catch (Exception $e ){
            throw new Exception('Transaction Error: '. substr($e->getMessage(),0,175));
        }
        return true;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('icas.auth');
    }

    /**
     * Display a list of all of the entities.
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $entities = $this->entityModel->get();

        if(null !== $this->getSortBy()) {
            $entities = $entities->sortBy($this->getSortBy());
        }
        if(null !== $this->getGroupBy()) {
            $entities = $entities->groupBy($this->getGroupBy());
        }
        return view($this->viewPath.'.index', ['entities' => $entities]);
    }

    /**
     * Display a entity.
     *
     * @param  Request $request
     * @return Response
     */
    public function show($id)
    {
        $entity = $this->entityModel->find($id);
        return view($this->viewPath . ".show", ['entity' => $entity, 'listOfValues'=>$this->getListOfValues()]);
    }

    /**
     * Edit an existing entity.
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $success = false;
        try {
            //todo: what happens if version is different?
            switch ($request->getMethod()){
            case AbstractAPIModel::PUT:
                $this->entityModel = $this->entityModel->find($id);
                $this->entityModel->fill($request->all());
                $success = $this->entityModel->put($id);
            case AbstractAPIModel::PATCH:
                $patch = $this->entityModel->fill($request->all());
                if($patch->patch($id)) {
                    return back()->withInput();
                }
            }
            if($success) {
                $request->session()->flash('message_success', 'Record has been successfully updated');
                $entity =  $this->entityModel;
            }
        }catch(\Exception $e){
            return redirect()->back()->withErrors("The update did not succeed:". $e->getMessage());
        }
        $request->session()->flash('message_success', 'Record has been successfully updated');
        return view($this->viewPath . ".show", ['entity' => $entity, 'listOfValues'=>$this->getListOfValues()]);
    }


    /**
     * Delete an existing entity.
     *
     * @param  Request $request
     * @return Response
     */
    public function destroy($entity, Request $request)
    {
        try {
            if($request->ajax()) {
                $status = $entity->delete();
                return response()->json($status);
            }
            if($request->input('_confirm') == 'TRUE') {
                if($entity->delete()) {
                    $request->session()->flash('message', 'Successfully deleted!');
                    return redirect($this->viewPath);
                }
                else {
                    throwException(new Exception("Could not delete"));
                }
            }
        }
        catch (Exception $e){
            $request->session()->flash('message_danger', 'Unknown Error: '. substr($e->getMessage(), 0, 175));
        }

        return view($this->viewPath . ".destroy", ['entity' => $entity]);
    }


    /**
     * Add entity to master record,  master foreign key must be part of request.
     *
     * Must pass if not ajax for route handling:
     *  a parent id for reference 'parent' => '<entity foreign id passed from source>'
     *  a route as hidden in post 'route'=>'<like a callback except pass a return route if not ajax>'
     */
    public function createAsDetail(Request $request)
    {
        $new = null;
        $id = $request->input($request->input('parent'));
        $errors = null;
        try{
            $new = $this->entityModel->create($request->all());
        }
        catch( Exception $e){
            if($request->ajax()) {
                return response()->json('errors not handled');
            }
            $request->session()->flash($id, 'Unknown Error: '. substr($e->getMessage(), 0, 175));
        }
        if($request->ajax()) {
            return response()->json($new);
        }

        return redirect()->route($request->input('route'), ['id' => $id])->withErrors($errors);
    }


    /**
     * @return mixed
     */
    public function getListOfValues()
    {
        return $this->listOfValues;
    }

    /**
     * @param string $group
     * @param mixed  $listOfValues
     */
    public function setListOfValues($group , $listOfValues)
    {
        $this->listOfValues[$group] = $listOfValues;
    }

    /**
     * Makes a paginator from a collection.
     *
     * @param array|Collection $items
     * @param int              $perPage
     * @param int              $page
     * @param array            $options
     *
     * @return LengthAwarePaginator
     */
    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function getControllerAction()
    {
        $route = Route::getCurrentRoute();
        $atSymbolPosition = strpos($route->getActionName(), "@");
        return substr($route->getActionName(), $atSymbolPosition+strlen("@"), strlen($route->getActionName()));
    }

}
