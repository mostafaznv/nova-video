<?php

namespace Mostafaznv\NovaVideo;

use Laravel\Nova\Fields\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Mostafaznv\NovaVideo\Traits\HandlesValidation;


class Video extends File
{
    use SupportsDependentFields, HandlesValidation;

    public $component   = 'video';
    public $textAlign   = 'center';
    public $showOnIndex = true;

    protected bool   $storeWithLarupload   = false;
    protected string $dir                  = 'ltr';
    protected string $playerType           = 'vidstack';
    protected bool   $displayCoverUploader = true;


    public function __construct($label, $fieldName = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($label, $fieldName, $disk, $storageCallback);


        $this->dir = config('nova-video.ui.player.dir');
        $this->playerType = config('nova-video.ui.player.type');
        $this->displayCoverUploader = config('nova-video.cover-uploader');


        $this->displayUsing(function($value, $model, $attribute) {
            return $this->storeWithLarupload
                ? $model->attachment($this->attribute)->urls()
                : $value;
        });

        $this->preview(function($value, $disk, $model) {
            if ($this->storeWithLarupload) {
                if ($model->id) {
                    $this->value = $model->attachment($this->attribute)->url();

                    return $this->value;
                }

                $this->value = null;

                return null;
            }

            return $value ? Storage::disk($this->getStorageDisk())->url($value) : null;
        });

        $this->thumbnail(function($value, $disk, $model) {
            if ($this->storeWithLarupload) {
                return $model->id
                    ? $model->attachment($this->attribute)->url('cover')
                    : null;
            }

            return $value ? Storage::disk($disk)->url($value) : null;
        });

        $this->delete(function(NovaRequest $request, $model) {
            if ($this->isPrunable()) {
                if ($this->storeWithLarupload) {
                    if ($request->query('cover') === 'true') {
                        $model->attachment($this->attribute)->cover()->detach();
                    }
                    else {
                        $model->attachment($this->attribute)->detach();
                    }
                }
                else if ($this->value) {
                    Storage::disk($this->getStorageDisk())->delete($this->value);

                    return $this->columnsThatShouldBeDeleted();
                }
            }

            return [];
        });

        $this->download(function($request, $model) {
            if ($this->storeWithLarupload) {
                return $model->attachment($this->attribute)->download();
            }

            $name = $this->originalNameColumn ? $model->{$this->originalNameColumn} : null;

            return Storage::disk($this->getStorageDisk())->download($this->value, $name);
        });

        $this->prunable();
    }

    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($this->storeWithLarupload) {
            $this->validate($request, $attribute);

            $originalAttribute = "$attribute.original";
            $coverAttribute = "$attribute.cover";

            if ($request->file($originalAttribute)) {
                $attribute = $originalAttribute;
                $requestAttribute = $originalAttribute;
            }
            else if ($request->file($coverAttribute)) {
                $attribute = $coverAttribute;
                $requestAttribute = $coverAttribute;
            }
        }


        return parent::fillAttribute($request, $requestAttribute, $model, $attribute);
    }

    public function storeWithLarupload(bool $status = true): Video
    {
        $this->storeWithLarupload = $status;

        if ($this->storeWithLarupload) {
            $this->storageCallback = function(NovaRequest $request, $model) {
                $attachment = $this->attribute;
                $file = $request->file("$attachment.original");
                $cover = $request->file("$attachment.cover");

                if ($file) {
                    $model->attachment($attachment)->attach($file, $cover);
                }
                else if ($request->isUpdateOrUpdateAttachedRequest() and $cover) {
                    if (!$model->attachment($attachment)->url()) {
                        abort(400);
                    }

                    $model->attachment($attachment)->cover()->update($cover);
                }

                return [];
            };
        }

        return $this;
    }

    public function dir(string $dir): self
    {
        $this->dir = $dir;

        return $this;
    }

    public function playerType(string $type): self
    {
        $this->playerType = $type;

        return $this;
    }

    public function hideCoverUploader(bool $status = true): self
    {
        $this->displayCoverUploader = !$status;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'laruploadIsOn'        => $this->storeWithLarupload,
            'dir'                  => $this->dir,
            'playerType'           => $this->playerType,
            'displayCoverUploader' => $this->displayCoverUploader
        ]);
    }
}
