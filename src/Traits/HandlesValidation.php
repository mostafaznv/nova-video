<?php

namespace Mostafaznv\NovaVideo\Traits;

use Laravel\Nova\Http\Requests\NovaRequest;
use Mostafaznv\NovaVideo\Enums\NovaVideoMode;


trait HandlesValidation
{
    public array $coverRules = [
        'nullable', 'file', 'mimetypes:image/jpeg,image/png', 'mimes:png,jpg,jpeg'
    ];

    public array $novaVideoRules         = [];
    public array $novaVideoCreationRules = [];
    public array $novaVideoUpdateRules   = [];


    /**
     * Validation rules for the cover files
     *
     * @param array $rules
     * @return \Mostafaznv\NovaFileArtisan\Fields\NovaFileArtisan|HandlesValidation
     */
    public function coverRules(array $rules): self
    {
        $this->coverRules = $rules;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function rules(...$rules): self
    {
        parent::rules($rules);

        if ($this->mode === NovaVideoMode::UPLOADED and $this->storeWithLarupload) {
            $this->novaVideoRules = $this->rules;
            $this->rules = [];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function creationRules(...$rules): self
    {
        parent::creationRules($rules);

        if ($this->mode === NovaVideoMode::UPLOADED and $this->storeWithLarupload) {
            $this->novaVideoCreationRules = $this->creationRules;
            $this->creationRules = [];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function updateRules(...$rules): self
    {
        parent::updateRules($rules);

        if ($this->mode === NovaVideoMode::UPLOADED and $this->storeWithLarupload) {
            $this->novaVideoUpdateRules = $this->updateRules;
            $this->updateRules = [];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRequired(NovaRequest $request): bool
    {
        if (!$this->storeWithLarupload) {
            return parent::isRequired($request);
        }

        return with($this->requiredCallback, function($callback) use ($request) {
            if ($callback === true || (is_callable($callback) && call_user_func($callback, $request))) {
                return true;
            }

            if (!empty($this->attribute) && is_null($callback)) {
                $rules = $this->novaVideoRules;
                $creationRules = array_merge_recursive($this->novaVideoCreationRules, $rules);
                $updateRules = array_merge_recursive($this->novaVideoUpdateRules, $rules);

                if ($request->isResourceIndexRequest() || $request->isLensRequest() || $request->isActionRequest()) {
                    return in_array('required', $creationRules);
                }

                if ($request->isCreateOrAttachRequest()) {
                    return in_array('required', $creationRules);
                }

                if ($request->isUpdateOrUpdateAttachedRequest()) {
                    return in_array('required', $updateRules);
                }
            }

            return false;
        });
    }

    /**
     * @inheritDoc
     */
    public function validationKey(): string
    {
        if ($this->mode === NovaVideoMode::UPLOADED and $this->storeWithLarupload) {
            return "$this->attribute.original";
        }

        return parent::validationKey();
    }

    /**
     * Custom validation for nova-file-artisan fields
     *
     * @param NovaRequest $request
     * @param string $attribute
     * @return void
     */
    protected function validate(NovaRequest $request, string $attribute): void
    {
        $originalAttribute = "$attribute.original";
        $coverAttribute = "$attribute.cover";

        $rules = $this->novaVideoRules;


        if ($request->isCreateOrAttachRequest()) {
            $creationRules = array_merge_recursive($this->novaVideoCreationRules, $rules);

            $request->validate([
                $originalAttribute => $creationRules,
                $coverAttribute    => $this->coverRules,
            ]);
        }
        else if ($request->isUpdateOrUpdateAttachedRequest()) {
            $updateRules = array_merge_recursive($this->novaVideoUpdateRules, $rules);

            $request->validate([
                $originalAttribute => $updateRules,
                $coverAttribute    => $this->coverRules,
            ]);
        }
    }
}
