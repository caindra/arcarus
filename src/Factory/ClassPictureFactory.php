<?php

namespace App\Factory;

use App\Entity\ClassPicture;
use App\Repository\ClassPictureRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ClassPicture>
 *
 * @method        ClassPicture|Proxy                     create(array|callable $attributes = [])
 * @method static ClassPicture|Proxy                     createOne(array $attributes = [])
 * @method static ClassPicture|Proxy                     find(object|array|mixed $criteria)
 * @method static ClassPicture|Proxy                     findOrCreate(array $attributes)
 * @method static ClassPicture|Proxy                     first(string $sortedField = 'id')
 * @method static ClassPicture|Proxy                     last(string $sortedField = 'id')
 * @method static ClassPicture|Proxy                     random(array $attributes = [])
 * @method static ClassPicture|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ClassPictureRepository|RepositoryProxy repository()
 * @method static ClassPicture[]|Proxy[]                 all()
 * @method static ClassPicture[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ClassPicture[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ClassPicture[]|Proxy[]                 findBy(array $attributes)
 * @method static ClassPicture[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ClassPicture[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ClassPictureFactory extends ModelFactory
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
            'description' => self::faker()->sentence(6),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(ClassPicture $classPicture): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ClassPicture::class;
    }
}
