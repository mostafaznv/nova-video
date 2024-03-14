---
description: mode
---

# Player Mode

<table><thead><tr><th width="167">Argument</th><th width="157">Type</th><th width="159" data-type="checkbox">Required</th><th>Accepted Values</th></tr></thead><tbody><tr><td>mode</td><td>enum</td><td>true</td><td><code>UPLOADED</code>, <code>URL</code></td></tr></tbody></table>

Beginning with <mark style="color:red;">v6.2</mark>, the Video field now supports URLs in addition to files. If you need to display videos from plain URLs, the URL mode is tailored for your requirements. It replaces the file uploader with a text field for ease of input.

```php
<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Mostafaznv\NovaVideo\Enums\NovaVideoMode;
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
                ->mode(NovaVideoMode::URL),
        ];
    }
}
```



