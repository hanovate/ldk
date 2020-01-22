<?php

namespace Unmit\ldk\Traits;
/*
 * This trait is to used with UNM API Model objects to manage components/relationships with API
 */

use Illuminate\Database\Eloquent\Collection;


trait HasComponentsTrait
{
    /*
     * This is the method that will be accessed from an API Model object to establish an accessor like
     * Laravel's Eloquent Relations. Will satisfy the function of $model->component(), where component is the
     * accessor methong on a model.
     *
     * This will allow the model to append an api to inject a url portion to orient
     * a api call to retrieve the model as a component.  Http client request url will
     * look as follows:
     * API_BASE_URL/apiEntity/<$url>/<$id>
     *
     * @param $modelName
     * @param $componentUrl
     * @param $apiForeignKeyField
     * @return Collection
     */
    public function hasComponents($modelName, $componentUrl = null, $apiForeignKeyMap)
    {
        $model = new $modelName();
        $urlKeySegment = null;
        if(is_array($apiForeignKeyMap)) {
            foreach ($apiForeignKeyMap as $key => $field){
                $urlKeySegment .= $key."/".$this->getAttribute($this->getPrefix().$field);
            }
        }else{
            $urlKeySegment = $this->getAttribute($apiForeignKeyMap);
        }
        $urlSegment = $model->apiEntity.'/'.$componentUrl.'/'.$urlKeySegment;
        $componentResponse = $model->httpClient->get($urlSegment);
        $collection = new Collection();
        // getArrayFromResponse is dependent on HttpClientUtilsTrait used in APIModel
        $componentResults = $model->getArrayFromResponse($componentResponse);
        foreach ($componentResults as $key=>$row){
            $component = new $modelName();
            $collection->add($component->fill($row));
        }
        return $collection;
    }

    /*
     *
     */
    /**
     * @param  $modelName
     * @param  $componentUrl
     * @param  $apiForeignKeyField
     * @return mixed
     */
    public function hasComponent($modelName, $componentUrl, $apiForeignKeyField)
    {
        $model = new $modelName();
        $urlSegment = $model->apiEntity.'/'.$componentUrl.'/'.$this->getAttribute($apiForeignKeyField);
        $componentResponse = $model->httpClient->get($urlSegment);
        $result = new $modelName($model->getArrayFromResponse($componentResponse));
        return $result;
    }

    /*

     *
     * @param $id
     * @param $url
     * @return Collection
     */
    public function isAComponent($ownerId, $url)
    {

    }

}