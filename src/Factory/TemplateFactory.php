<?php

namespace App\Factory;

use App\Entity\Template;
use App\Repository\TemplateRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Template>
 *
 * @method        Template|Proxy                     create(array|callable $attributes = [])
 * @method static Template|Proxy                     createOne(array $attributes = [])
 * @method static Template|Proxy                     find(object|array|mixed $criteria)
 * @method static Template|Proxy                     findOrCreate(array $attributes)
 * @method static Template|Proxy                     first(string $sortedField = 'id')
 * @method static Template|Proxy                     last(string $sortedField = 'id')
 * @method static Template|Proxy                     random(array $attributes = [])
 * @method static Template|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TemplateRepository|RepositoryProxy repository()
 * @method static Template[]|Proxy[]                 all()
 * @method static Template[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Template[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Template[]|Proxy[]                 findBy(array $attributes)
 * @method static Template[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Template[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TemplateFactory extends ModelFactory
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
            'layout' => self::faker()->image(),
            'organization' => OrganizationFactory::new(),
            'styleName' => self::faker()->word(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Template $template): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Template::class;
    }
}
