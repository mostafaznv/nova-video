# Installation

### Requirements:

* PHP 8.1 or higher
* Laravel 10.4.1 or higher
* Nova 4
* FFMPEG (optional)

{% hint style="info" %}
FFMPEG is required if you wish to use additional features with Larupload.
{% endhint %}

***



### Installation

#### 1. Install the package using composer

```sh
composer require mostafaznv/nova-video
```

#### 2. Publish the configuration file

```sh
php artisan vendor:publish --provider="Mostafaznv\NovaVideo\VideoFieldServiceProvider"
```

#### 3. That's it, Done



