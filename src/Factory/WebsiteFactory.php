<?php

namespace App\Factory;

use App\Entity\Website;
use App\Repository\WebsiteRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Website>
 *
 * @method        Website|Proxy                     create(array|callable $attributes = [])
 * @method static Website|Proxy                     createOne(array $attributes = [])
 * @method static Website|Proxy                     find(object|array|mixed $criteria)
 * @method static Website|Proxy                     findOrCreate(array $attributes)
 * @method static Website|Proxy                     first(string $sortedField = 'id')
 * @method static Website|Proxy                     last(string $sortedField = 'id')
 * @method static Website|Proxy                     random(array $attributes = [])
 * @method static Website|Proxy                     randomOrCreate(array $attributes = [])
 * @method static WebsiteRepository|RepositoryProxy repository()
 * @method static Website[]|Proxy[]                 all()
 * @method static Website[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Website[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Website[]|Proxy[]                 findBy(array $attributes)
 * @method static Website[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Website[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class WebsiteFactory extends ModelFactory
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
            'freeDeliveryOver' => self::faker()->randomFloat(),
            'websiteName' => self::faker()->text(60),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Website $website): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Website::class;
    }
}
