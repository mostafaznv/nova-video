<?php

namespace Mostafaznv\NovaVideo;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Illuminate\Support\Facades\Storage;
use Exception;
use Laravel\Nova\Fields\SupportsDependentFields;

class Video extends File
{
    use SupportsDependentFields;

    public $component   = 'video';
    public $textAlign   = 'center';
    public $showOnIndex = true;

    protected string $attachment = '';
    protected string $dir        = 'ltr';
    protected string $playerType = 'vidstack';

    public function __construct($label, $fieldName = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($label, $fieldName, $disk, $storageCallback);


        $this->dir = config('nova-video.ui.player.dir');
        $this->playerType = config('nova-video.ui.player.type');


        $this->displayUsing(function($value, $model, $attribute) {
            return $this->attachment ? $model->attachment($this->attachment)->urls() : $value;
        });

        $this->preview(function($value, $disk, $model) {
            if ($this->attachment and $model->id) {
                $this->value = $model->attachment($this->attachment)->url();

                return $this->value;
            }

            return $value ? Storage::disk($this->getStorageDisk())->url($value) : null;
        });

        $this->thumbnail(function($value, $disk, $model) {
            if ($this->attachment and $model->id) {
                return $model->attachment($this->attachment)->url('cover');
            }

            if (is_a($value, 'Mostafaznv\Larupload\Storage\Attachment') and $value->getName() === $this->attribute) {
                throw new Exception('Video attribute and Larupload attachment entity cannot be the same');
            }

            return $value ? Storage::disk($disk)->url($value) : null;
        });

        $this->delete(function(Request $request, $model) {
            if ($this->attachment) {
                $model->attachment($this->attachment)->detach();

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
                return $model->attachment($this->attachment)->download();
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

                $model->attachment($attachment)->attach($file, $cover ?? null);

                return [];
            };

            if ($this->isPrunable()) {
                $this->deleteCallback = function(Request $request, $model) use ($attachment) {
                    $model->attachment($this->attachment)->detach();

                    return [];
                };
            }
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


    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'laruploadIsOn' => !!$this->attachment,
            'dir'           => $this->dir,
            'playerType'    => $this->playerType,
        ]);
    }
}
