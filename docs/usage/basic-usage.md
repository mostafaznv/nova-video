# Basic Usage

1. **Add a String Column to Your Table**

Add a string column to your table. Here's an example of how to do it in a <mark style="color:red;">migration</mark> file:

```php
class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('video')->nullable();
            $table->timestamps();
        });
    }
}
```

2. **Add a Disk to config/filesystems.php**

In your `config/filesystems.php` file, add a disk configuration for <mark style="color:red;">media</mark> as follows:

{% code title="config/filesystems.php" %}
```php
'disks' => [
    'media' => [
        'driver'  => 'local',
        'root'    => public_path('uploads/media'),
        'url'     => env('APP_URL') . 'uploads/media'
    ]
],
```
{% endcode %}

3. **Add NovaVideo Field to Your Resource**

Here's an example of how to do it:

```php
<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
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
                ->rules('file', 'max:150000', 'mimes:mp4', 'mimetypes:video/mp4')
                ->creationRules('required')
                ->updateRules('nullable'),
        ];
    }
}
```

{% hint style="info" %}
* The second argument of the <mark style="color:red;">make</mark> function is your file column's name.
* The third argument of the <mark style="color:red;">make</mark> function is your preferred disk name.
{% endhint %}



4. **Enjoy**



