# Video Metadata

### Without Larupload

Even without using Larupload, you can still store and retrieve some essential video metadata. However, in this scenario, the available metadata is limited to the video's `fileName` and `fileSize`. You can achieve this using the built-in methods of Nova's File field.

```php
<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Mostafaznv\NovaVideo\Video;
use App\Models\IntroVideo as IntroVideoModel;
use Mostafaznv\NovaVideo\VideoMeta;

class IntroVideo extends Resource
{
    public static string $model = IntroVideoModel::class;

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Video::make(trans('Video'), 'video', 'local')
                ->storeOriginalName('video_file_name')
                ->storeSize('video_file_size'),

            Text::make('Video Name', 'video_file_name')->exceptOnForms(),

            Text::make('Size', 'video_file_size')
                ->exceptOnForms()
                ->displayUsing(
                    fn($value) => number_format($value / 1024, 2).'kb'
                ),
        ];
    }
}
```

{% hint style="info" %}
For more details, please refer to [Nova's documentation](https://nova.laravel.com/docs/resources/file-fields.html#storing-metadata).
{% endhint %}



***

### With Larupload

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

