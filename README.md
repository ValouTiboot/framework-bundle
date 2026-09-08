# Digitix Framework Bundle

Back-office generator for Symfony 6.4 / PHP 8.2: describe your entities in
`config/packages/digitix_framework.yaml`, get list / create / edit / delete
pages, filters, sorting, pagination, translations, permissions.

## Installation

```json
"require": { "digitix/framework-bundle": "@dev" },
"repositories": {
    "dev-package": { "type": "path", "url": "./packages/digitix/framework-bundle" }
}
```

```bash
composer require digitix/framework-bundle
cp packages/digitix/framework-bundle/recipe/digitix_framework.yaml config/packages/
cp packages/digitix/framework-bundle/recipe/routes/digitix_framework.yaml config/routes/
# merge recipe/security.yaml into config/packages/security.yaml
php bin/console doctrine:migrations:diff && php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load   # SuperAdmin user, default language, configuration rows
```

`translation.yaml`: set `default_locale: fr_FR`.

## Configuration

```yaml
digitix_framework:
  # optional, defaults shown
  entity_namespaces: ['App\Entity\', 'Digitix\FrameworkBundle\Entity\']
  controller_namespaces: ['App\Controller\Admin\', 'Digitix\FrameworkBundle\Controller\Admin\']

  admin_menu:
    cms: { title: Pages, route: dgtx_admin_entity_read, icon: web }
    parameters:
      title: Parametres
      route: ~
      icon: settings
      sub:
        user: { title: Utilisateurs, route: dgtx_admin_entity_read }

  admin_entities:
    Product:                                   # key = entity name
      class: App\Entity\Product                # optional, resolved by convention
      controller: App\Controller\Admin\ProductController   # optional, resolved by convention
      list:
        has_create: true
        items_per_page: 30
        actions: [edit, delete]                # view, view_entity, edit, delete
        fields:
          id:    { label: label.default.id, type: text, sort: true, default_sort: asc }
          name:  { label: label.product.name, type: text, sort: true }
          brand: { label: label.product.brand, type: entity, column: name }
        filters:
          name:  { label: label.product.name, type: text }
          brand: { label: label.product.brand, type: entity, collection: { name: Brand } }
      form:
        fields:
          translatableName: { label: label.product.name, name: name, type: translate }
          brand: { label: label.product.brand, type: entity, collection: { name: Brand, value: id } }
          photo: { label: label.product.photo, type: file }
          active: { label: label.default.active, type: bool, required: true }
```

Resolution by convention, in order:

- entity class: `App\Entity\Product`, then `Digitix\FrameworkBundle\Entity\Product`; none found = virtual entity (Dashboard, Parameter...)
- controller: `App\Controller\Admin\ProductController`, `App\Controller\Admin\AdminProductController`, then the same in the bundle, then the generic `AdminController`. A controller must extend `Digitix\FrameworkBundle\Controller\Admin\AdminController`.

Field types: `text`, `textarea`, `email`, `password`, `bool`, `choice`, `entity`, `date`, `hidden`, `translate`, `file`, `submit`, `button`, `search`.
Filter types: `text`, `email`, `bool`, `choice`, `entity`, `date`.
Unknown keys under a field or a filter are passed to the type, so custom types can define their own options.

## How it works

```
YAML ──(compile)──> AdminConfig / EntityConfig / ListConfig / FormConfig / FieldConfig
                                   │
request /admin/{entityName}/... ───┤ AdminRequestListener: resolve entity, pick controller, build AdminContext
                                   ▼
AdminController::read|create|edit|delete(AdminContext $context)
        │ AdminFormBuilder ── FieldTypeRegistry / FilterTypeRegistry
        │ ListQueryBuilder ── Sorter / Paginator
        │ ListViewBuilder / FormViewBuilder ── TemplateResolver
        ▼
Twig templates (@DigitixFramework/admin/...)
```

- `AdminConfig` is built once at container compile time and never changes at runtime.
- `AdminContext` is created per request, only on admin routes, and injected into controller actions. Services never read it from a constructor.
- Field and filter types are tagged services (`dgtx.admin.field_type`, `dgtx.admin.filter_type`): implement `FieldTypeInterface` / `FilterTypeInterface` in your project to add one.
- Permissions: `AdminVoter` grants everything to `ROLE_SUPERADMIN`, otherwise reads `Role::authorization`, e.g. `{"*": ["read"], "cms": ["read", "create", "edit"]}`.
- Delete is a POST with a CSRF token.

## Translations

Everything written in the code is English; back-office users translate it
from `/admin/translation`. The database is the source of truth, the
translator keeps reading files:

```
code ──(dgtx:translation:extract)──> translation table ──(compile)──> var/translations/<locale>/<Domain>.<locale>.php
                                            ▲                                   │
                                     admin editor                       RuntimeTranslator (translator.default)
```

- **Extraction**: Symfony's PHP (AST) and Twig extractors scan the bundle,
  `src/` and `templates/` of the project (`digitix_framework.translation.paths`),
  plus the labels declared in the digitix YAML. Keys must be literal strings,
  with the domain passed as a literal string too. One entry is created per
  key and per active language; new entries are pre-filled with the value the
  translator already knows (bundle defaults, legacy `translations/` files of
  the project). Keys that left the code are flagged `obsolete`, never deleted.
- **Compilation**: after every save the translated values are dumped into
  `digitix_framework.translation.output_dir` (default `var/translations`,
  never commit it) and the translator cache is invalidated. `RuntimeTranslator`
  loads those files even when they did not exist at container compile time,
  so no `cache:clear` is needed.
- Console: `dgtx:translation:extract [--locale=fr_FR] [--no-compile]`,
  `dgtx:translation:compile [locale]`. Run `extract` after each deployment
  (or click "refresh" in the admin) so that new keys show up.

Themes: give their templates a domain of their own (`'Theme.MyTheme'`) and
filter on it in the editor.

## Tests

The bundle ships its own test application (`tests/App`, SQLite in memory,
the shipped recipe as configuration) so it is tested independently of any
project:

```bash
composer install          # in the bundle directory
composer test             # PHPStan (level 6) then PHPUnit (unit + functional)
composer phpunit -- --filter CmsCrudTest
```

The GitHub Actions workflow runs the same command on PHP 8.2 and 8.4.

## Custom controller

```php
namespace App\Controller\Admin;

use Digitix\FrameworkBundle\Admin\Context\AdminContext;
use Digitix\FrameworkBundle\Admin\Security\AdminPermission;
use Digitix\FrameworkBundle\Controller\Admin\AdminController;

final class ProductController extends AdminController
{
    public function view(AdminContext $context): Response
    {
        $this->assertGranted(AdminPermission::VIEW, $context);

        return $this->renderAdmin('admin/product/dashboard.html.twig', $this->baseVars($context) + [
            'stats' => /* ... */,
        ]);
    }
}
```

Helpers available in a controller: `forms()`, `listView()`, `formView()`, `templates()`, `persister()`, `uploads()`, `configuration()`, `doctrine()`, `handleForm()`, `renderAdmin()`, `redirectToList()`, `trans()`.

`trans()` has the translator's signature, `trans($id, $parameters, $domain)`: always pass the domain as a literal string so that the translation extractor finds the key.
