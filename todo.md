# BUNDLE #
	> fichiers de config
		- builds dans webpack_encore.yaml pacakge apporteurImmo: '%kernel.project_dir%/public/themes/apporteurimmo/assets'
		- doctrine.yaml -> mapping_types: enum: string
		- security.yaml -> providers; firewalls; access_control
		- validator.yaml -> meme chose pour config/validator/validation.yaml
	> integrer webpack au bundle pour le BO plutot que encore - Optional
	> embarquer les themes au bunble - si theme classic
	> Add fixtures configuration
	> giter tout ca dans nouveau repo
	> faire un doc avec la procedure d'install et de config et l'utilisation du FRAMEWORK

# backEnd #
	> switch to debug
	> count dans liste
	> menu section avec widget
	> remove metaKeywords

# Frontend #
	> menu en widget
	> add js via controller
	> robots.txt in config

# FRAMEWORK #
	force hhtps on controllerRequest si https ok
	force https administrable
	revoir le build d'entity du context
	penser au fait que si l'instance exists elle est injectée
	revoir le build du form factory for more flexibility
	Revoir les listConfig et Form config
	form_themes not ok for front


# INSTALL#
	- git clone
	- composer config for digitix/framwork-bundle

	```
	"repositories": {
		"dev-package" :{
			"type": "path",
			"url": "./packages/digitix/framework-bundle"
		}
	}
	```

	- composer instal|update digitix/framework-bundle
	- recipes config
	- load fixture