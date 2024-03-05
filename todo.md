# TODO #
	revoir le systeme de digitixParameters avec un iterable countable avec des has et get
	virer les adminconfig|fieldconfig etc...
	revoir l'entity presenter et le context
	faire un vrai buildFilterForm
	revoir les style des traductions
	revoir les templates de l'admin include|extends|layout

# BUNDLE #
	> fichiers de config
		- builds dans webpack_encore.yaml pacakge apporteurImmo: '%kernel.project_dir%/public/themes/apporteurimmo/assets'
		- doctrine.yaml -> mapping_types: enum: string
		- security.yaml -> providers; firewalls; access_control
		- validator.yaml -> meme chose pour config/validator/validation.yaml
	> integrer webpack au bundle pour le BO plutot que encore - Optional
	> embarquer les themes au bunble - si theme classic
	> faire un doc avec la procedure d'install et de config et l'utilisation du FRAMEWORK

# backOffice #
	> revoir totalement les configs
	> robots.txt in config
	> regles d'opti apache
	> menu section avec widget
	> faire l'export
	> switch to debug
	> force hhtps on controllerRequest si https ok
	> force https administrable

# FrontOffice #
	> menu en widget
	> add js via controller

# FRAMEWORK #
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