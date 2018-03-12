# ProductShipping

Module is responsible for displaying available shipping methods for product view page using predefined country.

## Installation
1. Clone the repository
2. Run from console `composer install`

### Configuration
1. Go to Stores -> Configuration -> ClawRock -> Product Shipping
2. Select country which will be used to calculate available shipping methods, otherwise shipping methods won't be returned
3. You can also edit message that will be displayed when API returns empty array (shipping methods not found)

## API
You can get shipping methods for product using API request.
```
Endpoint: rest/V1/product-shipping-methods
Method: POST
```
#### Simple product
Body:
```
options: {
  "qty": 1,
  "sku": "S03"
}
```

---

#### Configurable product
Body:
```
options: {
  "super_attribute": {
    "142":"167",
    "93":"58"
  },
  "qty": 1,
  "sku": "WS03"
}
```
Keys in **super_attribute** array are super attribute ids, values are option ids, e.g. **142** is **color** attribute and **167** is **red** option.

---
#### Bundle product
Body:
```
options: {
  "bundle_option": {
    "1" : [3]
  },
  "bundle_option_qty": {
    "1": 5
  },
  "qty": 1,
  "sku": "24-WG080-1"
}
```
Keys in **bundle_option** array are option ids, values are selection ids. There might be several selections in one option i.e. multiple select, checkboxes.
Keys in **bundle_option_qty** array are options ids, values are qty of options.

## Tests
To run test run from console: `vendor/phpunit/phpunit/phpunit -c phpunit.xml.dist`
