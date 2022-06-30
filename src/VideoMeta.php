<?php

namespace Mostafaznv\NovaVideo;

use Laravel\Nova\Fields\Text;
use Mostafaznv\Larupload\Storage\Attachment;

class VideoMeta
{
    protected string $attachment;

    public static function make(string $attachment): self
    {
        return new static($attachment);
    }

    public function __construct(string $attachment)
    {
        $this->attachment = $attachment;
    }


    protected function meta(string $name, string $label): Text
    {
        $attachment = $this->attachment;

        return Text::make(trans($label), "{$attachment}_file_{$name}")
            ->readonly()
            ->exceptOnForms()
            ->displayUsing(function($value, $model) use ($attachment, $name) {
                if ($attachment and is_a($model->{$attachment}, Attachment::class)) {
                    $output = $model->{$attachment}->meta($name);

                    if ($name === 'duration') {
                        $output = $this->humanReadableDuration($output);
                    }
                    else if ($name === 'size') {
                        $output = $this->humanReadableBytes($output);
                    }
                }
                else {
                    $output = '-';
                }

                return $output;
            });
    }

    public function all(): array
    {
        return [
            $this->fileName(),
            $this->size(),
            $this->mimeType(),
            $this->format(),
            $this->width(),
            $this->height(),
            $this->duration(),
        ];
    }

    public function fileName(): Text
    {
        return $this->meta('name', 'File Name');
    }

    public function size(): Text
    {
        return $this->meta('size', 'Size');
    }

    public function mimeType(): Text
    {
        return $this->meta('mime_type', 'MIME Type');
    }

    public function width(): Text
    {
        return $this->meta('width', 'Width');
    }

    public function height(): Text
    {
        return $this->meta('height', 'Height');
    }

    public function duration(): Text
    {
        return $this->meta('duration', 'Duration');
    }

    public function format(): Text
    {
        return $this->meta('format', 'Format');
    }

    protected function humanReadableDuration(?int $seconds): string
    {
        if (is_null($seconds)) {
            return '—';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor($seconds / 60 % 60);
        $seconds = floor($seconds % 60);
        $output = [];

        if ($hours) {
            $output[] = $hours . 'h';
        }

        if ($minutes) {
            $output[] = $minutes . 'm';
        }

        if ($seconds) {
            $output[] = $seconds . 's';
        }

        return implode(' ', $output);
    }

    protected function humanReadableBytes(?int $bytes): string
    {
        if (is_null($bytes)) {
            return '—';
        }

        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
