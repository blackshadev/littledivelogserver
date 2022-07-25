<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Buddies\Services\BuddyCreator;
use App\Application\Buddies\Services\BuddyUpdater;
use App\Application\Dives\Services\DiveFinder;
use App\Application\Dives\Services\DiveTankCreator;
use App\Application\Dives\Services\DiveTankUpdater;
use App\Application\Dives\Services\Mergers\DiveEntityMerger;
use App\Application\Dives\Services\Mergers\DiveEntityMergerImpl;
use App\Application\Dives\Services\Mergers\DiveMerger;
use App\Application\Dives\Services\Mergers\DiveMergerImpl;
use App\Application\Dives\Services\Mergers\DiveSampleCombiner;
use App\Application\Dives\Services\Mergers\DiveSampleCombinerImpl;
use App\Application\Dives\Services\Mergers\DiveTankMerger;
use App\Application\Dives\Services\Mergers\DiveTankMergerImpl;
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
use App\Domain\Dives\Repositories\DiveBatchRepository;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\Repositories\DiveSummaryRepository;
use App\Domain\Dives\Repositories\DiveTankRepository;
use App\Domain\DiveSamples\DiveSamplesRepository;
use App\Domain\Equipment\Repositories\EquipmentRepository;
use App\Domain\Factories\Dives\DiveFactory;
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
use App\Repositories\Dives\EloquentDiveBatchRepository;
use App\Repositories\Dives\EloquentDiveFactory;
use App\Repositories\Dives\EloquentDiveRepository;
use App\Repositories\Dives\EloquentDiveSummaryRepository;
use App\Repositories\Dives\EloquentDiveTankRepository;
use App\Repositories\Dives\ExplorerDiveFinder;
use App\Repositories\DiveSamples\EloquentDiveSamplesRepository;
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

final class DiveServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(BuddyRepository::class, EloquentBuddyRepository::class);
        $this->app->singleton(DetailBuddyRepository::class, EloquentDetailBuddyRepository::class);
        $this->app->singleton(BuddyCreator::class);
        $this->app->singleton(BuddyUpdater::class);

        $this->app->singleton(TagRepository::class, EloquentTagRepository::class);
        $this->app->singleton(DetailTagRepository::class, EloquentDetailTagRepository::class);
        $this->app->singleton(TagCreator::class);
        $this->app->singleton(TagUpdater::class);

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

        $this->app->singleton(UpdatePasswordUpdater::class);
        $this->app->singleton(UpdateUserProfileUpdater::class);

        $this->app->singleton(DiveSummaryRepository::class, EloquentDiveSummaryRepository::class);
        $this->app->singleton(DiveSamplesRepository::class, EloquentDiveSamplesRepository::class);
        $this->app->singleton(DiveRepository::class, EloquentDiveRepository::class);
        $this->app->singleton(DiveBatchRepository::class, EloquentDiveBatchRepository::class);
        $this->app->singleton(DiveTankRepository::class, EloquentDiveTankRepository::class);
        $this->app->singleton(DiveTankCreator::class);
        $this->app->singleton(DiveTankUpdater::class);
        $this->app->singleton(DiveFinder::class, ExplorerDiveFinder::class);

        $this->app->singleton(DiveFactory::class, EloquentDiveFactory::class);
        $this->app->singleton(DiveMerger::class, DiveMergerImpl::class);
        $this->app->singleton(DiveEntityMerger::class, DiveEntityMergerImpl::class);
        $this->app->singleton(DiveTankMerger::class, DiveTankMergerImpl::class);
        $this->app->singleton(DiveSampleCombiner::class, DiveSampleCombinerImpl::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
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
