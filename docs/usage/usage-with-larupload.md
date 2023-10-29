# Usage with Larupload

{% hint style="info" %}
Larapload is a user-friendly package for managing media and uploads. To fully leverage the capabilities offered by NovaVideo, we recommend reading the Larapload [documentation](https://github.com/mostafaznv/larupload) first. This will ensure that you can take full advantage of the available abilities and seamlessly integrate media management and uploads into your Laravel Nova application.
{% endhint %}



Follow these steps to set up <mark style="color:red;">Larupload</mark> and integrate it with your project:

1. **Install Larupload using** [**this**](https://mostafaznv.gitbook.io/larupload/getting-started/installation) **documentation**
2. **Add Larupload Columns to Your Table:**

In your migration file, add Larupload columns to your table. Here's an example:

```php
<?php

use Mostafaznv\Larupload\LaruploadEnum;


class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->upload('video', LaruploadEnum::LIGHT_MODE);
            $table->timestamps();
        });
    }
}
```



3. **Add Larupload Trait to Your Model:**

Next, add the Larupload <mark style="color:red;">trait</mark> to your model. Define upload entities as needed. Here's an example:

```php
<?php

namespace App\Models;

use Mostafaznv\Larupload\Enums\LaruploadMediaStyle;
use Mostafaznv\Larupload\Enums\LaruploadMode;
use Mostafaznv\Larupload\Enums\LaruploadNamingMethod;
use Mostafaznv\Larupload\Storage\Attachment;
use Mostafaznv\Larupload\Traits\Larupload;

class Media extends Model
{
    use Larupload;


    public function attachments(): array
    {
        return [
            Attachment::make('video', LaruploadMode::LIGHT)
                ->namingMethod(LaruploadNamingMethod::HASH_FILE)
                ->coverStyle('cover', 852, 480, LaruploadMediaStyle::AUTO)
        ];
    }
}
```



4. **Add NovaVideo Field to Your Resource:**

In your Nova resource, add the `NovaVideo` field for the video column. Configure it as needed. Here's an example:

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

            Video::make(trans('Video'), 'video')
                ->storeWithLarupload()
                ->rules('file', 'max:150000', 'mimes:mp4', 'mimetypes:video/mp4')
                ->creationRules('required')
                ->updateRules('nullable'),
        ];
    }
}
```



{% hint style="info" %}
Larupload has its own disk, so the third argument of the make function (disk) is not used when you are using Larupload to handle the upload process.
{% endhint %}



