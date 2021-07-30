<?php

declare(strict_types=1);
//
//declare(strict_types=1);
//
//namespace App\Console\Commands;
//
//use App\Application\Buddies\DataTransferObjects\BuddyData;
//use App\Application\Dives\DataTransferObjects\DiveData;
//use App\Application\Equipment\DataTransferObjects\TankData;
//use App\Application\Tags\DataTransferObjects\TagData;
//use App\Domain\Support\Mapping\BasicMapping;
//use App\Domain\Support\Mapping\MappingInterface;
//use App\Models\Dive as DiveModel;
//use App\Models\Place;
//use App\Models\User;
//use Carbon\Carbon;
//use Illuminate\Console\Command;
//use Illuminate\Database\ConnectionInterface;
//use Illuminate\Support\Facades\Config;
//use Illuminate\Support\Facades\DB;
//
//class ImportDataNodeJS extends Command
//{
//    protected $signature = 'import:data:nodejs {--target=} {--driver=} {--port=} {--host=} {--database=} {--username=}';
//
//    protected $description = 'Imports data from the old data for the NodeJS backend';
//
//    private ConnectionInterface $source;
//
//    private DiveRepository $diveRepository;
//
//    public function __construct(
//        DiveRepository $diveRepository
//    ) {
//        parent::__construct();
//        $this->diveRepository = $diveRepository;
//    }
//
//    public function handle()
//    {
//        $connectionName = '__import_nodejs';
//        $password = env('IMPORT_DB_PASSWORD') ?: $this->secret('Password');
//
//        Config::set("database.connections." . $connectionName, [
//            "driver" => $this->option('driver') ?? env('IMPORT_DB_CONNECTION'),
//            "port" => $this->option('port') ?? env('IMPORT_DB_PORT'),
//            "host" => $this->option('host') ?? env('IMPORT_DB_HOST'),
//            "database" => $this->option('database') ?? env('IMPORT_DB_DATABASE'),
//            "username" => $this->option('username') ?? env('IMPORT_DB_USERNAME'),
//            "password" => $password
//        ]);
//
//        $this->source = DB::connection($connectionName);
//
//        $userMapping = $this->importUser();
//        $placeMapping = $this->importPlaces();
//        $buddyMapping = $this->importBuddies($userMapping);
//        $tagMapping = $this->importTags($userMapping);
//        $computerMapping = $this->importComputers($userMapping);
//        $this->importDives($userMapping, $computerMapping, $placeMapping, $buddyMapping, $tagMapping);
//    }
//
//    private function importUser(): MappingInterface
//    {
//        $users = $this->source->select("select * from users");
//        $userMapping = new BasicMapping();
//        foreach ($users as $user) {
//            $newUser = User::where('email', '=', $user->email)->first();
//            if ($newUser === null) {
//                $newUser = User::create([
//                    'email' => $user->email,
//                    'name' => $user->name,
//                    'password' => 'password',
//                    'created_at' => $user->inserted,
//                    'updated_at' => $user->inserted
//                ]);
//            }
//            $userMapping->set($user->user_id, $newUser->id);
//        }
//
//        return $userMapping;
//    }
//
//    private function importPlaces(): MappingInterface
//    {
//        $mapping = new BasicMapping();
//        $places = $this->source->select("select * from places");
//
//        foreach ($places as $place) {
//            $newPlace = Place::where([
//                'country_code' => $place->country_code,
//                'name' => $place->name
//            ])->first();
//
//            if ($newPlace === null) {
//                $newPlace = Place::create([
//                    'country_code' => $place->country_code,
//                    'name' => $place->name
//                ]);
//            }
//            $mapping->set($place->place_id, $newPlace->id);
//        }
//
//        return $mapping;
//    }
//
//    private function importBuddies(MappingInterface $userMapping): MappingInterface
//    {
//        $mapping = new BasicMapping();
//
//        foreach ($userMapping->all() as $oldUserId => $newUserId) {
//            $user = User::findOrFail($newUserId);
//            $buddies = $this->source->select(
//                "select * from buddies where user_id = ?",
//                [$oldUserId]
//            );
//
//            foreach ($buddies as $buddy) {
//                $newBuddy = $user->buddies()->updateOrCreate([
//                    'name' => $buddy->text,
//                    'color' => $buddy->color,
//                ]);
//                $mapping->set($buddy->buddy_id, $newBuddy->id);
//            }
//        }
//
//        return $mapping;
//    }
//
//    private function importTags(MappingInterface $userMapping): MappingInterface
//    {
//        $mapping = new BasicMapping();
//
//        foreach ($userMapping->all() as $oldUserId => $newUserId) {
//            $user = User::findOrFail($newUserId);
//            $tags = $this->source->select(
//                "select * from tags where user_id = ?",
//                [$oldUserId]
//            );
//
//            foreach ($tags as $tag) {
//                $newTag = $user->tags()->updateOrCreate([
//                    'text' => $tag->text,
//                    'color' => $tag->color,
//                ]);
//                $mapping->set($tag->tag_id, $newTag->id);
//            }
//        }
//
//        return $mapping;
//    }
//
//    private function importComputers(
//        MappingInterface $userMapping
//    ): MappingInterface {
//        $mapping = new BasicMapping();
//        foreach ($userMapping->all() as $oldUserId => $newUserId) {
//            $user = User::findOrFail($newUserId);
//            $computers = $this->source->select("select * from computers where user_id = ?", [$oldUserId]);
//
//            foreach ($computers as $computer) {
//                $newComputer = $user->computers()->updateOrCreate([
//                    'serial' => $computer->serial,
//                    'vendor' => $computer->vendor,
//                    'model' => $computer->model,
//                    'type' => $computer->type,
//                    'name' => $computer->name,
//                    'last_read' => $computer->last_read,
//                    'last_fingerprint' => $computer->last_fingerprint
//                ]);
//                $mapping->set($computer->computer_id, $newComputer->id);
//            }
//        }
//
//        return $mapping;
//    }
//
//    private function importDives(
//        MappingInterface $userMapping,
//        MappingInterface $computerMapping,
//        MappingInterface $placeMapping,
//        MappingInterface $buddyMapping,
//        MappingInterface $tagMapping
//    ): MappingInterface {
//        $mapping = new BasicMapping();
//        foreach ($userMapping->all() as $oldUserId => $newUserId) {
//            $user = User::findOrFail($newUserId);
//            $dives = $this->source->select(
//                "
//                     select dive_id
//                         , date
//                         , divetime
//                         , max_depth
//                         , to_json(tanks) as tanks
//                         , to_json((select array_agg(buddy_id) from dive_buddies db where db.dive_id = d.dive_id)) as buddies
//                         , to_json((select array_agg(tag_id) from dive_tags dt where dt.dive_id = d.dive_id)) as tags
//                         , place_id
//                         , updated
//                         , inserted
//                         , fingerprint
//                         , computer_id
//                         , samples
//                     from dives d
//                    where user_id = ?
//                    order by date asc
//                ",
//                [$oldUserId]
//            );
//
//            foreach ($dives as $dive) {
//                $newDive = $this->importDive($dive, $user, $computerMapping, $placeMapping, $buddyMapping, $tagMapping);
//                $mapping->set($dive->dive_id, $newDive->id);
//            }
//        }
//
//        return $mapping;
//    }
//
//    private function importDive(
//        \stdClass $oldDive,
//        User $user,
//        MappingInterface $computerMapping,
//        MappingInterface $placeMapping,
//        MappingInterface $buddyMapping,
//        MappingInterface $tagMapping
//    ): Dive {
//        $oldTagIds = $oldDive->tags ? json_decode($oldDive->tags) : [];
//        $oldBuddiesIds = $oldDive->buddies ? json_decode($oldDive->buddies) : [];
//        $oldTanks = $oldDive->tanks ? json_decode($oldDive->tanks) : [];
//        $samples = $oldDive->tanks ? json_decode($oldDive->samples, true) : [];
//        /** @var Dive $dive */
//        $dive = $user->dives()->where([
//            'date' => $oldDive->date,
//            'divetime' => $oldDive->divetime
//        ])->firstOrNew();
//
//        if ($dive->exists) {
//            $diveData = new DiveData();
//        } else {
//            $diveData = Dive::new();
//            $diveData->setUser($user);
//        }
//        $diveData->setDate(Carbon::parse($oldDive->date));
//        $diveData->setDivetime($oldDive->divetime);
//        $diveData->setMaxDepth((float)$oldDive->max_depth);
//        $diveData->getPlace()->setId($placeMapping->get($oldDive->place_id));
//
//        $buddyData = array_map(function ($oldBudId) use ($buddyMapping) {
//            $buddyData = new BuddyData();
//            $buddyData->setId($buddyMapping->get($oldBudId));
//            return $buddyData;
//        }, $oldBuddiesIds);
//        $diveData->setBuddies($buddyData);
//
//        $tagData = array_map(function ($oldTagId) use ($tagMapping) {
//            $buddyData = new TagData();
//            $buddyData->setId($tagMapping->get($oldTagId));
//            return $buddyData;
//        }, $oldTagIds);
//        $diveData->setTags($tagData);
//        $diveData->setSamples($samples);
//
//        if ($oldDive->computer_id) {
//            $diveData->setFingerprint($oldDive->fingerprint);
//            $diveData->setComputerId($computerMapping->get($oldDive->computer_id));
//        }
//
//        $tankData = array_map(function ($oldTankItem) {
//            $tank = new TankData();
//
//            $tank->setOxygen($oldTankItem->volume);
//            $tank->setVolume($oldTankItem->oxygen);
//            $tank->getPressures()->setBegin($oldTankItem->pressure->begin);
//            $tank->getPressures()->setEnd($oldTankItem->pressure->end);
//            $tank->getPressures()->setType($oldTankItem->pressure->type);
//
//            return $tank;
//        }, $oldTanks);
//
//        $diveData->setTanks($tankData);
//
//        $this->diveRepository->update($dive, $diveData);
//
//        return $dive;
//    }
//}
