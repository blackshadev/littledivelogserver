<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Buddies\Services\BuddyCreator;
use App\Application\Buddies\Services\BuddyUpdater;
use App\Application\Dives\Services\DiveFinder;
use App\Application\Dives\Services\DiveTankCreator;
use App\Application\Dives\Services\DiveTankUpdater;
use App\Application\Places\Services\PlaceFinder;
use App\Application\Tags\Services\TagCreator;
use App\Application\Tags\Services\TagUpdater;
use App\Application\Users\Services\UpdatePasswordUpdater;
use App\Application\Users\Services\UpdateUserProfileUpdater;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Buddies\Repositories\DetailBuddyRepository;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Computers\Repositories\DetailComputerRepository;
use App\Domain\Countries\Repositories\CountryRepository;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Dives\Repositories\DiveTankRepository;
use App\Domain\Equipment\Repositories\EquipmentRepository;
use App\Domain\Places\Repositories\PlaceRepository;
use App\Domain\Tags\Repositories\DetailTagRepository;
use App\Domain\Tags\Repositories\TagRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Domain\Users\Repositories\DetailUserRepository;
use App\Domain\Users\Repositories\PasswordRepository;
use App\Domain\Users\Repositories\UserRepository;
use App\Repositories\Buddies\EloquentBuddyRepository;
use App\Repositories\Buddies\EloquentDetailBuddyRepository;
use App\Repositories\Computers\EloquentComputerRepository;
use App\Repositories\Computers\EloquentDetailComputerRepository;
use App\Repositories\Countries\EloquentCountryRepository;
use App\Repositories\Dives\EloquentDiveRepository;
use App\Repositories\Dives\EloquentDiveSummaryRepository;
use App\Repositories\Dives\EloquentDiveTankRepository;
use App\Repositories\Dives\ExplorerDiveFinder;
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
        $this->app->singleton(BuddyCreator::class, BuddyCreator::class);
        $this->app->singleton(BuddyUpdater::class, BuddyUpdater::class);

        $this->app->singleton(TagRepository::class, EloquentTagRepository::class);
        $this->app->singleton(DetailTagRepository::class, EloquentDetailTagRepository::class);
        $this->app->singleton(TagCreator::class, TagCreator::class);
        $this->app->singleton(TagUpdater::class, TagUpdater::class);

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

        $this->app->singleton(UpdatePasswordUpdater::class, UpdatePasswordUpdater::class);
        $this->app->singleton(UpdateUserProfileUpdater::class, UpdateUserProfileUpdater::class);

        $this->app->singleton(DiveSummaryRepository::class, EloquentDiveSummaryRepository::class);
        $this->app->singleton(DiveRepository::class, EloquentDiveRepository::class);
        $this->app->singleton(DiveTankRepository::class, EloquentDiveTankRepository::class);
        $this->app->singleton(DiveTankCreator::class, DiveTankCreator::class);
        $this->app->singleton(DiveTankUpdater::class, DiveTankUpdater::class);
        $this->app->singleton(DiveFinder::class, ExplorerDiveFinder::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    public function provides()
    {
        return [
            BuddyCreator::class,
            BuddyUpdater::class,
        ];
    }
}