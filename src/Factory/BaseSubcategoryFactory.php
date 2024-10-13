<?php

namespace App\Factory;

use App\Entity\BaseSubcategory;
use App\Repository\BaseSubcategoryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<BaseSubcategory>
 *
 * @method        BaseSubcategory|Proxy                     create(array|callable $attributes = [])
 * @method static BaseSubcategory|Proxy                     createOne(array $attributes = [])
 * @method static BaseSubcategory|Proxy                     find(object|array|mixed $criteria)
 * @method static BaseSubcategory|Proxy                     findOrCreate(array $attributes)
 * @method static BaseSubcategory|Proxy                     first(string $sortedField = 'id')
 * @method static BaseSubcategory|Proxy                     last(string $sortedField = 'id')
 * @method static BaseSubcategory|Proxy                     random(array $attributes = [])
 * @method static BaseSubcategory|Proxy                     randomOrCreate(array $attributes = [])
 * @method static BaseSubcategoryRepository|RepositoryProxy repository()
 * @method static BaseSubcategory[]|Proxy[]                 all()
 * @method static BaseSubcategory[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static BaseSubcategory[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static BaseSubcategory[]|Proxy[]                 findBy(array $attributes)
 * @method static BaseSubcategory[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static BaseSubcategory[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class BaseSubcategoryFactory extends ModelFactory
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
            'category' => self::faker()->text(255),
            'replaceSubcategory' => self::faker()->text(255),
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
            // ->afterInstantiate(function(BaseSubcategory $baseSubcategory): void {})
        ;
    }

    protected static function getClass(): string
    {
        return BaseSubcategory::class;
    }
}
