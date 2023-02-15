# Installation

    add bundle to composer.json
    uner "require" section
    `"digitix/framework-bundle": "@dev",`

    And add new entry for local repository package

    `"repositories": {
        "dev-package" :{
            "type": "path",
            "url": "./packages/digitix/framework-bundle"
        }
    },
    `
    copy recipe files located un Resource/recipe

    then
    `composer require digitix/framework-bundle`

    Import database

    load fixture
    `php bin/console doctrine:fixtures:load`

    comment in assets.yaml
    ```yaml
    framework:
        assets:
            # json_manifest_path: '%kernel.project_dir%/public/build/manifest.json'
    ```

    translation.yaml > change locale to fr_FR
    
