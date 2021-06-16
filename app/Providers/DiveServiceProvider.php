<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Buddies\Repositories\DetailBuddyRepository;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Computers\Repositories\DetailComputerRepository;
use App\Domain\Countries\Repositories\CountryRepository;
use App\Domain\Equipment\Repositories\EquipmentRepository;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Domain\Places\Services\PlaceFinder;
use App\Domain\Tags\Repositories\DetailTagRepository;
use App\Domain\Tags\Repositories\TagRepository;
use App\Domain\Users\Mutators\UpdatePasswordMutator;
use App\Domain\Users\Mutators\UpdateUserProfileMutator;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Domain\Users\Repositories\DetailUserRepository;
use App\Domain\Users\Repositories\PasswordRepository;
use App\Domain\Users\Repositories\UserRepository;
use App\Repositories\Buddies\EloquentBuddyRepository;
use App\Repositories\Buddies\EloquentDetailBuddyRepository;
use App\Repositories\Computers\EloquentComputerRepository;
use App\Repositories\Computers\EloquentDetailComputerRepository;
use App\Repositories\Countries\EloquentCountryRepository;
use App\Repositories\Equipment\EloquentEquipmentRepository;
use App\Repositories\Places\EloquentPlacesRepositories;
use App\Repositories\Tags\EloquentDetailTagRepository;
use App\Repositories\Tags\EloquentTagRepository;
use App\Repositories\Users\EloquentDetailUserRepository;
use App\Repositories\Users\EloquentUserRepository;
use App\Repositories\Users\LaravelCurrentUserRepository;
use App\Repositories\Users\LaravelPasswordRepository;
use App\Services\Places\ExplorerPlaceFinder;
use Illuminate\Support\ServiceProvider;

class DiveServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BuddyRepository::class, EloquentBuddyRepository::class);
        $this->app->singleton(DetailBuddyRepository::class, EloquentDetailBuddyRepository::class);

        $this->app->singleton(TagRepository::class, EloquentTagRepository::class);
        $this->app->singleton(DetailTagRepository::class, EloquentDetailTagRepository::class);

        $this->app->singleton(ComputerRepository::class, EloquentComputerRepository::class);
        $this->app->singleton(DetailComputerRepository::class, EloquentDetailComputerRepository::class);

        $this->app->singleton(CountryRepository::class, EloquentCountryRepository::class);

        $this->app->singleton(PlaceRepository::class, EloquentPlacesRepositories::class);
        $this->app->singleton(PlaceFinder::class, ExplorerPlaceFinder::class);

        $this->app->singleton(EquipmentRepository::class, EloquentEquipmentRepository::class);

        $this->app->singleton(CurrentUserRepository::class, LaravelCurrentUserRepository::class);
        $this->app->singleton(DetailUserRepository::class, EloquentDetailUserRepository::class);
        $this->app->singleton(UserRepository::class, EloquentUserRepository::class);
        $this->app->singleton(PasswordRepository::class, LaravelPasswordRepository::class);

        $this->app->singleton(UpdatePasswordMutator::class, UpdatePasswordMutator::class);
        $this->app->singleton(UpdateUserProfileMutator::class, UpdateUserProfileMutator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
