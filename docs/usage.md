# Usage

- [Getting Mapper instance](#getting-mapper-instance)
    - [As independent library](#as-independent-library)
    - [As Symfony Bundle](#as-symfony-bundle)
- [What can I do map with Mapper?](#what-can-i-do-map-with-mapper)
    - [From array to DTO Object](#from-array-to-dto-object)
    - [From DTO Object to Some other object, by providing classname](#from-dto-object-to-some-other-object-by-providing-classname)
    - [From Object to DTO](#from-object-to-dto)
- [Some additional features](#some-additional-features)
    - [Specifying on which property to map to.](#specifying-on-which-property-to-map-to)
    - [Using custom Setter when mapping on a destination object](#using-custom-setter-when-mapping-on-destination-object)
    - [Ignoring properties](#ignoring-properties)
    - [Transforming value](#transforming-value)
        - [Stacking transformers](#stacking-transformers)
        - [Built-in transformers](#built-in-transformers)
    - [Request to DTO](#request-to-dto)
    - [Mapping to Doctrine Entity](#mapping-to-doctrine-entity)
        - [Updating an existing entity](#updating-an-existing-entity)
        - [Creating a new entity](#creating-a-new-entity)
        - [Important notes](#important-notes)
    - [Constructor strategy](#constructor-strategy)
        - [Available Strategies](#available-strategies)

## Getting Mapper instance

### As independent library
```php
$mapper = ControlBit\Dto\Factory::create();
$mapper->map($source, $destination);
```

### As Symfony Bundle
You can use Mapper service directly by service ID `dto_bundle.mapper` or inject autowired using
`ControlBit\Dto\Contract\Mapper\MapperInterface`

```php
use ControlBit\Dto\Contract\Mapper\MapperInterface;

class YourService {
    public function __construct(private readonly MapperInterface $mapper) {      
    }
    
    private function foo() {
        $this->mapper->map($yourSource, YourDto::class)
    }
}
```

## What can I do map with Mapper?

Mapper supports the next cases of mapping:

| Source | Destination | Description                                                                                                                                                                                                                  |
| :--- | :--- |:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `array` | `object` | Mapping from associative `array` to `object` created by providing `classname` or already instantiated object. Be noted that if you provide object as destination, some properties may be overwritten.                        |
| `Object` | `DTO object` | Mapping from `Object` to DTO object by providing `classname` or provide already instantiated object. or already instantiated object. Be noted that if you provide object as destination, some properties may be overwritten. |
| `Request` | `object` | Mapping from Http `Request`  to `object` created by providing `classname`. This comes out of the box only if you use as Symfony bundle with proper atributes.                                                                |

Let's see some examples down there.

### From array to DTO Object
It's basically a DeNormalizer, but with some extra features.
If you use it as Array to DTO object, it can go nested.
```php
$source = [
    'bar' => 1,
    'baz' => 2,
    'nested' => [
        'bar' => 3,
        'baz' => 4,
    ],
    'arrayOfFoo' => [
        [ 'bar' => 5,'baz' => 6],
        [ 'bar' => 7,'baz' => 8],
    ],
];

class Dto {
    private int $bar;
    private int $baz;
    private ?Dto $nested;
    
    /* We explain that array elements are DTOs of this class */
    #[ControlBit\Dto\Attribute\Dto(Dto::class)] 
    private array $arrayOfFoo;
}

$newObject = $mapper->map($source, Foo::class);
```

### From DTO Object to Some other object, by providing classname
.
```php

class Dto {
    private int $bar;
    private int $baz;
}

$source = new Dto(/* some args */);

class Foo {
    private int $bar;
    private int $baz;
    
    private array $arrayOfFoo;
}

$newObject = $mapper->map($source, Foo::class);
```

You can achieve this without passing destination, by putting `#[Dto(Foo::class)]` on top of your Dto class,
and calling just:
```php
    $newObject = $mapper->map($source);
```
This is useful if you want to constrain your DTO to be mapped only to certain type of object.

### From Object to DTO
Similar to the previous one, but reversed. Also, let's make it litle more complex:
```php

class Foo {
    private int $bar;
    private int $baz;
    private ?Foo $nested;
    private array $arrayOfFoo;
}

$source = new Foo(/* some args */);

class Dto {
    private int $bar;
    private int $baz;
    private Dto $nested;
    
    /* We explain that array elements are DTOs of this class */
    #[ControlBit\Dto\Attribute\Dto(Dto::class)]
    private array $arrayOfFoo;
}

$newObject = $mapper->map($source, Dto::class);
```

Now, you can see that there's another nested dto, of the same class (but could be another).
Also, you can see an array of DTOs there. 
The DTO Mapper will look at the types you provide in destination and will try to map them accordingly.
For arrays, as DTO Mapper doesn't know what type of object you want to put in it, as you can se in case of `$arrayOfFoo`.


## Some additional features

### Specifying on which property to map to.
Usually when mapping, in 90% cases your source property name is the same as destination property name.
But, sometimes you want to be specific on which property you want to map to and 
you can achieve this by adding `ControlBit\Dto\Attribute\To` attribute on source property.


```php

use ControlBit\Dto\Attribute\To;

class OrderDto {
    #[To(member: 'priceIncludingVat')]
    private float $price;
}

class Order {
    private float $priceIncludingVat;
}
```

### Using custom Setter when mapping on destination object
Let's say you want to map one property to another, but you want to use custom setter on a destination object.
You can achieve this by adding `ControlBit\Dto\Attribute\To` to property on a Source object.
One thing here that is cons is that you have to know on what object you want to map in advance, when making source.

```php

use ControlBit\Dto\Attribute\To;

class OrderDto {
    #[To(setter: 'setPriceWithVAT')]
    private float $price;
}

class Order {
    private float $price;
    private float $priceIncludingVat;
    private float $vatOnly;
    
    /* This method will be called by Mapper to set DTO value to */
    public function setPriceIncludingVAT(int $price) {
        $this->prioce            = $price;    
        $this->priceIncludingVat = $price * 1.2;
        $this->vatOnly           = $price * 0.2;    
        /* This is just an example, in real life, be careful with floats :) */
    }
}
```

### Ignoring properties
Rare case, but sometimes you want to ignore some properties on a source or destination object.
Some practical reason for that is you want to avoid accidental mapping of some sensitive data, which you want to
populate later

In that case we can use `ControlBit\Dto\Attribute\Ignore` attribute on source property.
You can also put it on destination property.

```php

use ControlBit\Dto\Attribute\Ignore;

class UserDto {
    private string $username; 

    #[Ignore]
    private int $secretCode;    
}

class User {
    private string $username;
    
    /* THIS WILL NOT BE MAPPED! */
    private int $secretCode;   
}
```

⚠️ This would violate a programming principle, 
that DTO should always be in valid state and immutable, so use with caution.

### Transforming value
Very often, you will want to transform value before it's mapped to destination object.

In that case we can use `ControlBit\Dto\Attribute\Transformer` attribute on source property.
Attribute requires a constructor argument that must be
a class (FQCN) that implements `ControlBit\Dto\Contract\TransformerInterface`

Interface consists of 2 mandatory methods by it's contract, 
and when you, for example, want to make EnumTransformer, you will need to implement theese 2 methods:
- `transform()` - You are providing transformation from string to Enum.
- `reverse()` - Opposite, you are providing transformation from Enum to string.

To use `reverse` transformation, in attribute, pass `reverse: true` in $options argument.

Let's see one example for transformer that multiplies value by 1000:

```php

use ControlBit\Dto\Contract\Transformer\TransformerInterface;

final class PriceMultiplier implements TransformerInterface
{
    /**
     * @param  float  $value
     */
    public function transform(mixed $value, array $options = []): mixed
    {
        return $value * 1000;
    }
    
    public function reverse(mixed $value, array $options = []): mixed
    {
        return $value / 1000;
    }
}

```

```php
use ControlBit\Dto\Attribute\Transformer;

class OrderDto {
    #[Transformer(PriceMultiplier::class)]
    private float $price;
}

class Order {
    /* `Price` will have value that is previously transformed, in this case with VAT included. */
    private float $price;
}
```

#### Stacking transformers
You can stack transformers on a single property.
Each transformer will be called in order, and result of previous will be passed to next.
It could be useful when you want to transform value from Enum, and than apply some other transformation, 
for example translation.

#### Built-in transformers
We've built some transformers for you out of the box. 
They could be put on destination property.

| Attribute | Description                                                                                                                                                                                                                                                                              |
| --- |------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `ControlBit\Dto\Attribute\Transformers\Collection` | Transforms source's array to Doctrine ArrayCollection on destination.                                                                                                                                                                                                                    |
| `ControlBit\Dto\Attribute\Transformers\Enum` | Transforms source's int\|string value to Enum on Desination.                                                                                                                                                                                                                             |
| `ControlBit\Dto\Attribute\Transformers\FirstElementOfArray` | Uses first element of source array as value for destination.                                                                                                                                                                                                                             |
| `ControlBit\Dto\Attribute\Transformers\Translate` | Translates source string to desired language on destination. You can provide optionally `domain`, `locale` and `modifier`. Some examples for modifier:`#[Translate(modifier: 'strtoupper')]` or `#[Translate(modifier: [YourStaticClass::class, 'YourStaticMethod', ['Arg1','Arg2']])]`. |

### Request to DTO

If you would use DTO Mapper as Symfony bundle, you can make your request mapped into
DTO object using `#[RequestDto]` attribute, that will passed to your controller action.

```php
use ControlBit\Dto\Attribute\Dto;

final class YourController {

    private function fooAction(#[RequestDto] YourDtoClass $dto) {
        /* Your request data will be mapped to Dto object*/
    }
}
```

When DTO Mapper parses your request, all parts of request data will be mapped into DTO object.
That includes Query, Body and Files.

IF you want to map only some parts of request, you can define in `#[RequestDto]`
attribute which parts should be mapped via $parts argument.
Examples of RequestDto Attribute:
```#[RequestDto(parts: [RequestPart::QUERY, RequestPart::BODY])]```
```#[RequestDto(parts: ['query', 'body'])]```


⚠️ IMPORTANT!
- DTO Argument resolver validate your DTO if `Symfony Validator` is installed and throw `ControlBit\Dto\Exception\ValidationException`.
- Mentioned exception will be automatically rendered as JSON response with error message unless `validation_json_bad_request` is set to false (default is true). See configuration reference.

### Mapping to Doctrine Entity

DTO Mapper can map your DTO directly to a Doctrine Entity. This is particularly useful for update operations where 
you want to fetch an existing entity from the database and update its fields with the data from your DTO.

To map to a Doctrine Entity, use the `entityClass` parameter of the `#[Dto]` attribute on your DTO class. You also need to mark the property that holds the entity identifier with the `#[Identifier]` attribute.

#### Updating an existing entity

If the property marked with `#[Identifier]` has a value, DTO Mapper will try to fetch the 
entity from the database using that identifier.

```php
use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Attribute\Identifier;
use App\Entity\Product;

#[Dto(entityClass: Product::class)]
class ProductUpdateDto
{
    #[Identifier]
    public int $id;

    public string $name;

    public float $price;
}
```

When you map this DTO:
```php
$dto = new ProductUpdateDto();
$dto->id = 1;
$dto->name = 'Updated Product Name';
$dto->price = 19.99;

$product = $mapper->map($dto);
// $product is now the instance of Product with ID 1, with updated name and price.
```

#### Creating a new entity

If the property marked with `#[Identifier]` is null or not provided, DTO Mapper will create a new instance of the entity class.

```php
#[Dto(entityClass: Product::class)]
class ProductCreateDto
{
    public string $name;
    public float $price;
}

$dto = new ProductCreateDto();
$dto->name = 'New Product';
$dto->price = 9.99;

$product = $mapper->map($dto);
// $product is a new instance of Product.
```

#### Important notes
- The `#[Identifier]` attribute is required for DTO Mapper to know which property to use as the database ID when fetching an existing entity.
- If you use DTO Mapper as a Symfony bundle and have `doctrine/orm` installed, the `EntityManager` will be automatically used to fetch entities.
- If an entity with the given identifier is not found, a `ControlBit\Dto\Exception\EntityNotFoundException` will be thrown.

### Constructor strategy

The DTO Mapper uses different strategies to instantiate your objects. By default, it uses the `OPTIONAL` constructor strategy.

You can customize this behavior using the `constructorStrategy` parameter of the `#[Dto]` attribute on your DTO class, or globally when creating the mapper.

#### Available Strategies

The following strategies are available via the `ControlBit\Dto\Enum\ConstructorStrategy` enum:

| Strategy   | Description                                                                                                                                                           |
|:-----------|:----------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `OPTIONAL` | **(Default)** Will use constructor for properties which are available to set via constructor. For others, it will map directly to props.                              |
| `ALWAYS`   | Will always use the constructor. If any required constructor argument is missing in the source, a `ControlBit\Dto\Exception\MissingArgumentException` will be thrown. |
| `NEVER`    | Will never use the constructor. The object will be instantiated without calling the constructor (using reflection only), and properties will be mapped directly.      |
