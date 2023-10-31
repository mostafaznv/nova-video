---
description: dir
---

# Direction (dir)

<table><thead><tr><th width="167">Argument</th><th width="157">Type</th><th width="159" data-type="checkbox">Required</th><th>Accepted Values</th></tr></thead><tbody><tr><td>dir</td><td>enum</td><td>true</td><td><code>LTR</code>, <code>RTL</code></td></tr></tbody></table>

The <mark style="color:red;">dir</mark> option allows you to define the layout direction of the video player when displaying videos using the vidstack player.

```php
<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Mostafaznv\NovaVideo\Enums\NovaVideoPlayerDirection;
use Mostafaznv\NovaVideo\Video;
use App\Models\Media as MediaModel;


class Media extends Resource
{
    public static string $model = MediaModel::class;


    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Video::make(trans('Video'), 'video')
                ->storeWithLarupload()
                ->dir(NovaVideoPlayerDirection::LTR),
        ];
    }
}
```



