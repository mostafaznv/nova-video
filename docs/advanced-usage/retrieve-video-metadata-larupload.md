# Retrieve Video Metadata (Larupload)

You can easily retrieve extracted metadata from videos using Larupload. This feature is specifically designed to work seamlessly with <mark style="color:red;">Larupload</mark>.





{% tabs %}
{% tab title="Retrieve All Metadata" %}
```php
<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Mostafaznv\NovaVideo\Video;
use App\Models\Media as MediaModel;
use Mostafaznv\NovaVideo\VideoMeta;


class Media extends Resource
{
    public static string $model = MediaModel::class;


    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Video::make(trans('Video'), 'video')->storeWithLarupload(),
           
            ...VideoMeta::make('video')->all(),
        ];
    }
}
```
{% endtab %}

{% tab title="Select Preferred Metadata " %}
```php
<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Mostafaznv\NovaVideo\Video;
use App\Models\Media as MediaModel;
use Mostafaznv\NovaVideo\VideoMeta;


class Media extends Resource
{
    public static string $model = MediaModel::class;


    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Video::make(trans('Video'), 'video')->storeWithLarupload(),

            VideoMeta::make('video')->fileName(),
            VideoMeta::make('video')->size(),
            VideoMeta::make('video')->mimeType(),
            VideoMeta::make('video')->width(),
            VideoMeta::make('video')->height(),
            VideoMeta::make('video')->duration(),
            VideoMeta::make('video')->format(),*/

        ];
    }
}
```
{% endtab %}
{% endtabs %}

