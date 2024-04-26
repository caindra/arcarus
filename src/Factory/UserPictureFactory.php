<?php

namespace App\Factory;

use App\Entity\UserPicture;
use App\Repository\UserPictureRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<UserPicture>
 *
 * @method        UserPicture|Proxy                     create(array|callable $attributes = [])
 * @method static UserPicture|Proxy                     createOne(array $attributes = [])
 * @method static UserPicture|Proxy                     find(object|array|mixed $criteria)
 * @method static UserPicture|Proxy                     findOrCreate(array $attributes)
 * @method static UserPicture|Proxy                     first(string $sortedField = 'id')
 * @method static UserPicture|Proxy                     last(string $sortedField = 'id')
 * @method static UserPicture|Proxy                     random(array $attributes = [])
 * @method static UserPicture|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UserPictureRepository|RepositoryProxy repository()
 * @method static UserPicture[]|Proxy[]                 all()
 * @method static UserPicture[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static UserPicture[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static UserPicture[]|Proxy[]                 findBy(array $attributes)
 * @method static UserPicture[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static UserPicture[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UserPictureFactory extends ModelFactory
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
        $description = self::faker()->optional(0.5, false)->word();
        return [
            'image' => self::faker()->image(),
            'description' => $description
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(UserPicture $userPicture): void {})
        ;
    }

    protected static function getClass(): string
    {
        return UserPicture::class;
    }
}
