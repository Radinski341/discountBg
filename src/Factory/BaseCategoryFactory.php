<?php

namespace App\Factory;

use App\Entity\BaseCategory;
use App\Repository\BaseCategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<BaseCategory>
 *
 * @method        BaseCategory|Proxy                     create(array|callable $attributes = [])
 * @method static BaseCategory|Proxy                     createOne(array $attributes = [])
 * @method static BaseCategory|Proxy                     find(object|array|mixed $criteria)
 * @method static BaseCategory|Proxy                     findOrCreate(array $attributes)
 * @method static BaseCategory|Proxy                     first(string $sortedField = 'id')
 * @method static BaseCategory|Proxy                     last(string $sortedField = 'id')
 * @method static BaseCategory|Proxy                     random(array $attributes = [])
 * @method static BaseCategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static BaseCategoryRepository|RepositoryProxy repository()
 * @method static BaseCategory[]|Proxy[]                 all()
 * @method static BaseCategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static BaseCategory[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static BaseCategory[]|Proxy[]                 findBy(array $attributes)
 * @method static BaseCategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static BaseCategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class BaseCategoryFactory extends ModelFactory
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
            'replaceCategory' => self::faker()->text(255),
            'title' => self::faker()->text(255),
            'website' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(BaseCategory $baseCategory): void {})
        ;
    }

    protected static function getClass(): string
    {
        return BaseCategory::class;
    }
}
