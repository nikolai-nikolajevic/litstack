<?php

namespace Fjord\Crud\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Fjord\Crud\Requests\CrudCreateRequest;
use Fjord\Crud\Requests\CrudUpdateRequest;

trait CrudBaseApi
{
    /**
     * Update Crud model.
     *
     * @param CrudUpdateRequest $request
     * @param string|integer $id
     * * @param string $form_name
     * @return mixed $model
     */
    public function update(CrudUpdateRequest $request, $id, $form_name)
    {
        $form = $this->getForm($form_name) ?: abort(404);
        $model = $this->findOrFail($id);
        $this->validate($request, $this->getForm($form_name));

        $this->fillModelAttributes($model, $request, $form->getRegisteredFields());
        $attributes = $this->filterRequestAttributes($request, $form->getRegisteredFields());

        $model->update($attributes);

        if ($model->last_edit) {
            $model->load('last_edit');
        }

        return crud($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fjord\Crud\Requests\CrudCreateRequest  $request
     * @return mixed
     */
    public function store(CrudCreateRequest $request, $form_name)
    {
        $form = $this->getForm($form_name) ?: abort(404);
        //$this->validate($request, $form);
        //dd($form->getRegisteredFields());
        $attributes = $this->filterRequestAttributes($request, $form->getRegisteredFields());
        dd($attributes);

        if ($this->config->sortable) {
            $attributes[$this->config->orderColumn] = $this->query()->count() + 1;
        }

        $model = $this->model::create($attributes);

        return crud($model);
    }
}
