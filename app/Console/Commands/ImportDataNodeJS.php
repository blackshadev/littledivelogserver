<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ImportDataNodeJS extends Command
{
    protected $signature = 'import:data:nodejs {--target=} {--driver=pgsql} {--port=} {--host=} {--database=} {--username=}';

    protected $description = 'Imports data from the old data for the NodeJS backend';

    private ConnectionInterface $source;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $connectionName = '__import_nodejs';
        $password = $this->secret('Password: ');
        Config::set("database.connections." . $connectionName, [
            "driver" => $this->option('driver'),
            "port" => $this->option('port'),
            "host" => $this->option('host'),
            "database" => $this->option('database'),
            "username" => $this->option('username'),
            "password" => $password
        ]);

        $this->source = DB::connection($connectionName);

        dd($this->importUser());
    }

    private function importUser(): array
    {
        $users = $this->source->select("select * from users");
        $userMapping = [];
        foreach ($users as $user) {
            $newUser = User::where('email', '=', $user->email)->first();
            if ($newUser === null) {
                $newUser = User::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'password' => 'password',
                    'created_at' => $user->inserted,
                    'updated_at' => $user->inserted
                ]);
            }
            $userMapping[$user->user_id] = $newUser->id;
        }

        return $userMapping;
    }
}
