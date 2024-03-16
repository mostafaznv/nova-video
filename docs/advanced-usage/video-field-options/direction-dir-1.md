---
description: maxHeight
---

# Max Height

<table><thead><tr><th width="204">Argument</th><th width="117">Type</th><th width="159" data-type="checkbox">Required</th><th>Example</th></tr></thead><tbody><tr><td>height</td><td>string</td><td>true</td><td><code>auto</code>, <code>200px</code></td></tr></tbody></table>

The <mark style="color:red;">ui.player.max-height</mark> property enables you to specify the maximum height for the player on the <mark style="color:red;">**index page**</mark> of your Nova resource.

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
                ->maxHeight('160px'),
        ];
    }
}
```



