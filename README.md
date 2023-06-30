# Video Field for Laravel Nova

[![Total Downloads](https://img.shields.io/packagist/dt/mostafaznv/nova-video.svg?style=flat-square)](https://packagist.org/packages/mostafaznv/nova-video)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/mostafaznv/nova-video.svg?style=flat-square)](https://packagist.org/packages/mostafaznv/nova-video)

Upload and display videos in laravel nova

This package is an *extended* version of built-in nova *file field* that helps you to upload local video files and display them in nova panel.

You don't need any extra package to use `NovaVideo` field. But if you need more features, we suggest you to use `Larupload` next to the `NovaVideo`.

`NovaVideo` comes with built-in support for [Larupload](https://github.com/mostafaznv/larupload/) package


## Base features:

- Upload videos
- Display videos using html5 video tag
- Delete videos
- Replace videos
- Download videos
- Localization
- Configurable


## Additional features with Larupload:

- Attach a poster to video files
- Extract video metadata such as duration, width, height, and dominant color
- Ability to resize/crop photos and videos
- Ability to create HTTP Live Streaming (HLS) from video sources


----
I am on an open-source journey 🚀, and I wish I could solely focus on my development path without worrying about my financial situation. However, as life is not perfect, I have to consider other factors.

Therefore, if you decide to use my packages, please kindly consider making a donation. Any amount, no matter how small, goes a long way and is greatly appreciated. 🍺

[![Donate](https://mostafaznv.github.io/donate/donate.svg)](https://mostafaznv.github.io/donate)

----

## Requirements:

- PHP 8.1 or higher
- Laravel 10.4.1 or higher
- FFMPEG (optional)

> FFMPEG is required if you wish to use additional features with Larupload.

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
        'url'     => env('APP_URL') . '/uploads/media'
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

> The second argument of `make` function is your file column's name.  

> The third argument of `make` function is your preferred disk name.


## Usage with Larupload

1- Install `Larupload` using [this](https://mostafaznv.gitbook.io/larupload/getting-started/installation) documentation

2- Add Larupload columns to your table

```
use Mostafaznv\Larupload\Enums\LaruploadMode;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->upload('video', LaruploadMode::LIGHT_MODE);
            $table->timestamps();
        });
    }
}
```

3- Add larupload trait to your model

```
use Mostafaznv\Larupload\Enums\LaruploadMediaStyle;
use Mostafaznv\Larupload\Enums\LaruploadMode;
use Mostafaznv\Larupload\Enums\LaruploadNamingMethod;
use Mostafaznv\Larupload\Storage\Attachment;
use Mostafaznv\Larupload\Traits\Larupload;

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
            Attachment::make('video', LaruploadMode::LIGHT)
                ->namingMethod(LaruploadNamingMethod::HASH_FILE)
                ->coverStyle('cover', 852, 480, LaruploadMediaStyle::AUTO)
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
> When you have defined a Larupload attachment entity in your model, you should avoid using the same name for your Nova fields as the entity. As you can see in the code above, the second argument of the make function is `videos`, not `video`.

> Larupload has its own disk, so the third argument of make function (disk) is not used when you are using larupload to handle upload process.



5- Configure`Larupload`to use the same disk as `NovaVideo`.

Go to `config/larupload.php` and set:

```
'disk' => 'media'
```
> Make sure that the disk exists in the config/filesystem.php file, check [usage section](#usage) on step 2



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
Check Larupload [documentation](https://mostafaznv.gitbook.io/larupload/)   


## Nova Field Notable Methods
| Name               | Arguments                                  | description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
|--------------------|--------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| storeWithLarupload | string (required) (attachment entity name) | Handle the whole upload process with `Larupload`                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| prunable           | boolean                                    | The prunable method will instruct Nova to delete the underlying file from storage when the associated model is deleted from the database.<br><br> **Note**: If you are using larupload, you have to keep in mind that larupload will delete files automatically after each delete. to control it, take a look at the larupload documentation and read about `preserve file property`                                                                                                                                                                                                                                                                                                                                             |
| make               | label (field's label), field name, disk    | **Label**: Defines a label for file field <br><br> **Field Name**: Defines the name of input element `<input type='file' />` <br> — **Without Larupload**: you must provide the name of the file column as the Field Name parameter. <br> — **With Larupload**: when you have defined a larupload attachment entity in your model, you can't use the name of that entity for this argument. use whatever you want, but not the entity's name  <br><br> **Disk**: name of your preferred disk in config/filesystems.php file. <br> Note: Larupload has its own disk, so this argument is not used when you are using larupload to handle upload process. check larupload [documentation](https://github.com/mostafaznv/larupload) |

----
I am on an open-source journey 🚀, and I wish I could solely focus on my development path without worrying about my financial situation. However, as life is not perfect, I have to consider other factors.

Therefore, if you decide to use my packages, please kindly consider making a donation. Any amount, no matter how small, goes a long way and is greatly appreciated. 🍺

[![Donate](https://mostafaznv.github.io/donate/donate.svg)](https://mostafaznv.github.io/donate)

----

## License
This software released under [Apache License Version 2.0](LICENSE.txt).

(C) 2023 Mostafaznv, All rights reserved.
