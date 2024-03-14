---
description: playerType
---

# Player Type

<table><thead><tr><th width="167">Argument</th><th width="157">Type</th><th width="159" data-type="checkbox">Required</th><th>Accepted Values</th></tr></thead><tbody><tr><td>type</td><td>enum</td><td>true</td><td><code>VIDSTACK</code>, <code>DEFAULT</code></td></tr></tbody></table>

Starting from <mark style="color:red;">v6.0</mark>, the Video field uses the [vidstack](https://www.vidstack.io/) video player as the default option for displaying videos. However, you still have the flexibility to choose the default HTML player if you prefer it. The choice is yours, and you can easily select your preferred player type to enhance your video playback experience.

```php
<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Mostafaznv\NovaVideo\Enums\NovaVideoPlayerType;
use Mostafaznv\NovaVideo\Video;
use App\Models\Media as MediaModel;


class Media extends Resource
{
    public static string $model = MediaModel::class;


    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Video::make(trans('Video'), 'video', 'media')
                ->playerType(NovaVideoPlayerType::DEFAULT),
        ];
    }
}
```



