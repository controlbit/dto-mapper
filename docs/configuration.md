# Symfony configuration reference

You don't need it unless you want to override something.
```yaml
dto_bundle:
  # Throws JSON error message When DTO is invalid
  validation_json_bad_request: true
  map_private_properties: true # Should map private properties
  # that you are using REST API Snake case, and PascalCase in your DTO object props.  
  case_transformer: ControlBit\Dto\Adapter\CaseTransformer\SnakeCaseToCamelcaseTransformer
```
