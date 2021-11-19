<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Cronjob Laravel

Laravel's command scheduler allows you to fluently and expressively define your command schedule within Laravel itself. When using the scheduler, only a single Cron entry is needed on your server. This responsitory introduces you how to create a simple Cronjob.

You can read more about Taks scheduling [here](https://laravel.com/docs/8.x/scheduling)

## Setup project

First create new project, In your terminal, cd to your root folder of your server. I assume you have composer installed. Then run this command:
```console
composer create-project laravel/laravel Cronjob-demo-laravel
```

## Create Model

```console
php artisan make:model Cron
```

Copy and paste this code into ..\app\Models\, Cron.php.

```console
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{
    use HasFactory;

    protected $guarded = [];
}

```

## Create Command

After installation is complete, cd into your project and run this command:
```console
php artisan make:command GetPrice
```

Copy and paste this code into ..\app\Console\Commands, GetPrice.php.

```console
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use App\Models\Cron;

class GetPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves the current price of Bitcoin from coindesk.com public API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get('https://api.coindesk.com/v1/bpi/currentprice.json');

        $info = [
            'usd' => $response['bpi']['USD']['rate_float'],
            'eur' => $response['bpi']['EUR']['rate_float']
        ];

        $price = Cron::create($info);

        $this->info(
            'Saved Bitcoin price: ' .
            $price->usd . ' USD, and ' .
            $price->eur . ' EUR'
        );

        return 0;
    }
}
```

This code takes some data from the "coindesk" API. To use in our example.

## Migrates

You must create a table in the database using the following code below:

```console
php artisan make:migration create_crons_table
```

Take the name of the file that was generated and copy and paste this code into ..\database\migrations 

```console
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crons', function (Blueprint $table) {
            $table->id();
            $table->decimal('usd', 9, 2);
            $table->decimal('eur', 9, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crons');
    }
}
```

After installation is complete, cd into your project and run this command:
```console
php artisan migrate
```

## Run Cronjob

First, run this command to check if your Cronjob is registered to Artisan. You must see it in the list:
```console
php artisan list
```
After your command registered to the command, run the below command:
```console
php artisan price:get
```

## More

What if you want to set the crone job to run automatically without initiating using command. Just run this command:
```console
crontab -e
```

This will open server crontab file, paste this code inside, save it and exit.
```console
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```
