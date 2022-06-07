# Video Field for Laravel Nova

Upload and display videos in laravel nova

This package is an *extended* version of built-in nova *file field* that helps you to upload local video files and display them in nova panel.

You don't need any extra package to use `NovaVideo` field. But if you need more features, we suggest you to use `Larupload` next to the `NovaVideo`.

`NovaVideo` comes with built-in support for `Larupload` package


## Base features:

- Upload videos
- Display videos using html5 video tag
- Delete videos
- Replace videos
- Download videos
- Localization
- Configurable


## Additional features with Larupload:

- Attach poster to video files
- Extract video metadata such as duration, width, height and dominant color
- Ability to resize/crop photos and videos
- Ability to create HTTP Live Streaming (HLS) from video sources


----
🚀 If you find this project interesting, please consider supporting me on the open source journey

[![Donate](https://mostafaznv.github.io/donate/donate.svg)](https://mostafaznv.github.io/donate)

----

## Requirements:

- PHP 8.0.2 or higher
- Laravel 9.* or higher
- FFMPEG (not required)


## Installation

Install using composer:

```
composer require mostafaznv/nova-video
```


## Usage

1- Add a string column to your table

```
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

2- Add a disk to `config/filesystems.php`

```
'disks' => [
    'media' => [
        'driver'  => 'local',
        'root'    => public_path('uploads/media'),
        'url'     => env('APP_URL') . 'uploads/media'
    ]
],
```

3- Add `NovaVideo` field to your Resource

```
use Mostafaznv\NovaVideo\Video;

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

> **Note**: The second argument of `make` function is your file column's name.  
> **Note**: The third argument of `make` function is your preferred disk name


## Usage with Larupload

1- Install `Larupload` using [this](https://github.com/mostafaznv/larupload/#installation) documentation

2- Add Larupload columns to your table

```
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

3- Add larupload trait to your model

```
use Mostafaznv\Larupload\Storage\Attachment;
use Mostafaznv\Larupload\Traits\Larupload;
use Mostafaznv\Larupload\LaruploadEnum;

class Media extends Model
{
    use Larupload;


    /**
     * Define Upload Entities
     *
     * @return array
     * @throws Exception
     */
    public function attachments(): array
    {
        return [
            Attachment::make('video', LaruploadEnum::LIGHT_MODE)
                ->namingMethod(LaruploadEnum::HASH_FILE_NAMING_METHOD)
                ->coverStyle(852, 480, LaruploadEnum::AUTO_STYLE_MODE)
        ];
    }
}
```

4- Add `NovaVideo` field to your `Resource`

```
use Mostafaznv\NovaVideo\Video;

class Media extends Resource
{
    public static string $model = MediaModel::class;

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Video::make(trans('Video'), 'videos')
                ->rules('file', 'max:150000', 'mimes:mp4', 'mimetypes:video/mp4')
                ->storeWithLarupload('video')
                ->creationRules('required')
                ->updateRules('nullable'),
        ];
    }
}
```

> **Note**: When you have defined a larupload attachment entity in your model, you can't use the name of that entity for your nova fields. as you can see in above code, the second argument of make function is `videos`, not `video`.

> **Note**: Larupload has its own disk, so the third argument of make function (disk) is not used when you are using larupload to handle upload process.  


## Get Video Metadata (Larupload)
You can print extracted metadata from videos. this feature only works with larupload

```
<?php
use Mostafaznv\NovaVideo\Video;
use Mostafaznv\NovaVideo\VideoMeta;

class Media extends Resource
{
    public static string $model = MediaModel::class;

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Video::make(trans('Video'), 'videos')->storeWithLarupload('video'),

            // print all metadata with this function
            
            ...VideoMeta::make('video')->all(),

            // or print them by their function
            
            /*VideoMeta::make('video')->fileName(),
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

## Rest API Usage (Larupload)
check larupload [documentation](https://github.com/mostafaznv/larupload)   


## Nova Field Notable Methods
| Name               | Arguments                                  | description                                                                                                                                                                                                                                                                                                                                                                         |
|--------------------|--------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| storeWithLarupload | string (required) (attachment entity name) | handle whole upload process larupload                                                                                                                                                                                                                                                                                                                                               |
| prunable           | boolean                                    | The prunable method will instruct Nova to delete the underlying file from storage when the associated model is deleted from the database.<br><br> **Note**: If you are using larupload, you have to keep in mind that larupload will delete files automatically after each delete. to control it, take a look at the larupload documentation and read about preserve file property  |
| make               | label (field's label), field name, disk    | **Label**: Defines a label for file field <br><br> **Field Name**: Defines the name of <input type='file' \/><br> - without larupload: should be your file column's name <br> - with larupload: when you have defined a larupload attachment entity in your model, you can't use the name of that entity for this argument. use whatever you want, but not the entity's name  <br><br> **Disk**: name of your preferred disk in config/filesystems.php file. <br> Note: Larupload has its own disk, so this argument is not used when you are using larupload to handle upload process. check larupload [documentation](https://github.com/mostafaznv/larupload)     |

----
🚀 If you find this project interesting, please consider supporting me on the open source journey

[![Donate](https://mostafaznv.github.io/donate/donate.svg)](https://mostafaznv.github.io/donate)

----

## Changelog
Refer to the [Changelog](CHANGELOG.md) for a full history of the project.

## License
This software released under [Apache License Version 2.0](LICENSE.txt).

(C) 2022 Mostafaznv, All rights reserved.
