<?php
declare(strict_types=1);

namespace ControlBit\Dto;

use ControlBit\Dto\Adapter\ArrayAdapter;
use ControlBit\Dto\Adapter\CaseTransformer\SnakeCaseToCamelCaseTransformer;
use ControlBit\Dto\Adapter\MapAdapter;
use ControlBit\Dto\Adapter\ObjectAdapter;
use ControlBit\Dto\Adapter\RequestAdapter;
use ControlBit\Dto\Builder\Builder;
use ControlBit\Dto\Contract\Mapper\CaseTransformerInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Finder\AccessorFinder;
use ControlBit\Dto\Finder\SetterFinder;
use ControlBit\Dto\Finder\SetterType\Direct;
use ControlBit\Dto\Finder\SetterType\MapTo;
use ControlBit\Dto\Finder\SetterType\ViaSetter;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\Mapper\ValueConverter;
use ControlBit\Dto\Mapper\ValueConverter\ArrayOfDto;
use ControlBit\Dto\Mapper\ValueConverter\ArrayToObject;
use ControlBit\Dto\MetaData\MethodMetadataFactory;
use ControlBit\Dto\MetaData\ObjectMetadataFactory;
use ControlBit\Dto\MetaData\PropertyMetadataFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class Factory
{
    /**
     * @param  class-string  $caseTransformer
     */
    public static function create(string $caseTransformer = SnakeCaseToCamelCaseTransformer::class): Mapper
    {
        if (!\class_exists($caseTransformer)) {
            throw new InvalidArgumentException(
                \sprintf('Transformer class "%s" does not exist.', $caseTransformer)
            );
        }

        /** @var CaseTransformerInterface $caseTransformer */
        $caseTransformer         = new $caseTransformer();
        $accessorFinder          = new AccessorFinder();
        $propertyMetaDataFactory = new PropertyMetadataFactory($accessorFinder);
        $methodMetaDataFactory   = new MethodMetadataFactory();
        $objectMetadataFactory   = new ObjectMetadataFactory($propertyMetaDataFactory, $methodMetaDataFactory);
        $mapAdapter              = new MapAdapter(
            [
                new RequestAdapter($caseTransformer),
                new ObjectAdapter(),
                new ArrayAdapter(),
            ]
        );
        $setterFinder            = new SetterFinder(
            [
                new  MapTo(),
                new  ViaSetter(),
                new  Direct(),
            ],
        );

        $valueConverter = new ValueConverter(
            [
                new ArrayOfDto(),
                new ArrayToObject(),
            ]
        );

        $builder = new Builder($valueConverter);

        return new Mapper($objectMetadataFactory, $mapAdapter, $setterFinder, $builder);
    }
}