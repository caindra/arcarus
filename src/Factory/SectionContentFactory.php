<?php

namespace App\Factory;

use App\Entity\SectionContent;
use App\Repository\SectionContentRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<SectionContent>
 *
 * @method        SectionContent|Proxy                     create(array|callable $attributes = [])
 * @method static SectionContent|Proxy                     createOne(array $attributes = [])
 * @method static SectionContent|Proxy                     find(object|array|mixed $criteria)
 * @method static SectionContent|Proxy                     findOrCreate(array $attributes)
 * @method static SectionContent|Proxy                     first(string $sortedField = 'id')
 * @method static SectionContent|Proxy                     last(string $sortedField = 'id')
 * @method static SectionContent|Proxy                     random(array $attributes = [])
 * @method static SectionContent|Proxy                     randomOrCreate(array $attributes = [])
 * @method static SectionContentRepository|RepositoryProxy repository()
 * @method static SectionContent[]|Proxy[]                 all()
 * @method static SectionContent[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static SectionContent[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static SectionContent[]|Proxy[]                 findBy(array $attributes)
 * @method static SectionContent[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static SectionContent[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class SectionContentFactory extends ModelFactory
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
            'classPicture' => ClassPictureFactory::new(),
            'section' => SectionFactory::new(),
            'title' => self::faker()->word(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(SectionContent $sectionContent): void {})
        ;
    }

    protected static function getClass(): string
    {
        return SectionContent::class;
    }
}
