<?php

namespace App\Factory;

use App\Entity\MainCategory;
use App\Repository\MainCategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<MainCategory>
 *
 * @method        MainCategory|Proxy                     create(array|callable $attributes = [])
 * @method static MainCategory|Proxy                     createOne(array $attributes = [])
 * @method static MainCategory|Proxy                     find(object|array|mixed $criteria)
 * @method static MainCategory|Proxy                     findOrCreate(array $attributes)
 * @method static MainCategory|Proxy                     first(string $sortedField = 'id')
 * @method static MainCategory|Proxy                     last(string $sortedField = 'id')
 * @method static MainCategory|Proxy                     random(array $attributes = [])
 * @method static MainCategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static MainCategoryRepository|RepositoryProxy repository()
 * @method static MainCategory[]|Proxy[]                 all()
 * @method static MainCategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static MainCategory[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static MainCategory[]|Proxy[]                 findBy(array $attributes)
 * @method static MainCategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static MainCategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class MainCategoryFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->unique()->words(3, true),
            'slug' => self::faker()->slug()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(MainCategory $mainCategory): void {})
        ;
    }

    protected static function getClass(): string
    {
        return MainCategory::class;
    }
}
