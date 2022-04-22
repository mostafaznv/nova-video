<?php

namespace Mostafaznv\NovaVideo;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Illuminate\Support\Facades\Storage;
use Exception;

class Video extends File
{
    public $component   = 'video';
    public $textAlign   = 'center';
    public $showOnIndex = true;

    protected string $attachment = '';

    public function __construct($label, $fieldName = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($label, $fieldName, $disk, $storageCallback);

        $this->displayUsing(function($value, $model, $attribute) {
            return $this->attachment ? $model->{$this->attachment}->urls() : $value;
        });

        $this->preview(function($value, $disk, $model) {
            if ($this->attachment and $model->id) {
                return $model->{$this->attachment}->url();
            }

            return $value ? Storage::disk($this->getStorageDisk())->url($value) : null;
        });

        $this->thumbnail(function($value, $disk, $model) {
            if ($this->attachment and $model->id) {
                return $model->{$this->attachment}->url('cover');
            }

            if (is_a($value, 'Mostafaznv\Larupload\Storage\Attachment') and $value->getName() === $this->attribute) {
                throw new Exception('Video attribute and Larupload attachment entity cannot be the same');
            }

            return $value ? Storage::disk($disk)->url($value) : null;
        });

        $this->delete(function(Request $request, $model) {
            if ($this->attachment) {
                $model->{$this->attachment}->detach();

                return [];
            }
            else if ($this->value) {
                Storage::disk($this->getStorageDisk())->delete($this->value);

                return $this->columnsThatShouldBeDeleted();
            }

            return [];
        });

        $this->download(function($request, $model) {
            if ($this->attachment) {
                return $model->{$this->attachment}->download();
            }

            $name = $this->originalNameColumn ? $model->{$this->originalNameColumn} : null;

            return Storage::disk($this->getStorageDisk())->download($this->value, $name);
        });

        $this->prunable();
    }

    /**
     * Specify if we want whole uploading process to be handled with larupload.
     *
     * @param string $attachment
     * @return $this
     * @throws Exception
     */
    public function storeWithLarupload(string $attachment = ''): Video
    {
        if ($attachment) {
            if ($attachment == $this->attribute) {
                throw new Exception('Video attribute and Larupload attachment entity cannot be the same');
            }

            $this->attachment = $attachment;

            $this->storageCallback = function(Request $request, $model) use ($attachment) {
                $file = $request->file($this->attribute);
                $cover = $request->file($this->attribute . '_larupload_cover');

                $model->{$attachment}->attach($file, $cover ?? null);

                return [];
            };

            if ($this->isPrunable()) {
                $this->deleteCallback = function(Request $request, $model) use ($attachment) {
                    $model->{$this->attachment}->detach();

                    return [];
                };
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'laruploadIsOn' => !!$this->attachment
        ]);
    }
}
