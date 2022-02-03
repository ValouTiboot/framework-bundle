<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Digitix\FrameworkBundle\Utils\Cache;
use Digitix\FrameworkBundle\Orm\Paginator;
use Digitix\FrameworkBundle\Sorter\Sorter;
use Digitix\FrameworkBundle\Context\Context;
use Digitix\FrameworkBundle\Helper\HelperForm;
use Digitix\FrameworkBundle\Helper\HelperList;
use Digitix\FrameworkBundle\Helper\HelperView;
use Digitix\FrameworkBundle\Factory\FormFactory;
use Digitix\FrameworkBundle\Orm\EntityPersister;
use Symfony\Component\Form\FormFactoryInterface;
use Digitix\FrameworkBundle\Factory\FieldFactory;
use Digitix\FrameworkBundle\Factory\EntityFactory;
use Digitix\FrameworkBundle\Factory\FilterFactory;
use Digitix\FrameworkBundle\Factory\SearchFactory;
use Digitix\FrameworkBundle\Factory\SorterFactory;
use Digitix\FrameworkBundle\Factory\ContextFactory;
use Digitix\FrameworkBundle\Sorter\SorterInterface;
use Digitix\FrameworkBundle\Field\Type\PasswordType;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Digitix\FrameworkBundle\Factory\PaginatorFactory;
use Digitix\FrameworkBundle\Finder\TranslationFinder;
use Digitix\FrameworkBundle\Provider\ContextProvider;
use Digitix\FrameworkBundle\DataFixtures\UserFixtures;
use Digitix\FrameworkBundle\Factory\HelperFormFactory;
use Digitix\FrameworkBundle\Factory\HelperListFactory;
use Digitix\FrameworkBundle\Factory\HelperViewFactory;
use Digitix\FrameworkBundle\Presenter\EntityPresenter;
use Digitix\FrameworkBundle\Helper\HelperListInterface;
use Digitix\FrameworkBundle\Updater\TranslationUpdater;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Digitix\FrameworkBundle\Repository\EntityRepository;
use Digitix\FrameworkBundle\Security\AdminAuthenticator;
use Digitix\FrameworkBundle\Provider\TranslationProvider;
use Digitix\FrameworkBundle\DataFixtures\LanguageFixtures;
use Digitix\FrameworkBundle\EventListener\ContextListener;
use Digitix\FrameworkBundle\Factory\TranslationFormFactory;
use Digitix\FrameworkBundle\Provider\ConfigurationProvider;
use Digitix\FrameworkBundle\EventListener\ControllerListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Digitix\FrameworkBundle\EventListener\AdminAccessListener;
use Digitix\FrameworkBundle\Provider\EntityRepositoryProvider;
use Digitix\FrameworkBundle\EventListener\AdminControllerListener;
use Digitix\FrameworkBundle\DependencyInjection\DigitixFrameworkExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->autowire()

        ->set(AdminAuthenticator::class)
            ->arg(0, new Reference('doctrine.orm.default_entity_manager'))
            ->arg(1, new Reference('router.default'))
            ->arg(2, new Reference('security.csrf.token_manager'))
            ->arg(3, new Reference('security.user_password_encoder.generic'))

        ->set(AdminControllerListener::class)
            ->arg(0, new Reference('twig'))
            ->arg(1, new Reference('security.helper'))
            ->arg(2, new Reference(DigitixFrameworkExtension::ALIAS_ADMIN_MENU_CONFIG))
            ->tag('kernel.event_listener', ['event' => RequestEvent::class])
            ->tag('kernel.event_listener', ['event' => ControllerEvent::class])

        ->set(AdminAccessListener::class)
            ->tag('kernel.event_listener', ['event' => ControllerEvent::class])

        ->set(Context::class)
            ->arg(0, new Reference('security.token_storage', ContainerInterface::NULL_ON_INVALID_REFERENCE))
            ->arg(1, new Reference('twig'))
            ->arg(2, new Reference('translator'))

        ->set(ContextFactory::class)
            ->arg(0, new Reference('security.token_storage'))
            ->arg(1, new Reference('twig'))
            ->arg(2, new Reference('translator'))

        ->set(EntityPresenter::class)

        ->set('dgtx.entity.repository.provider', EntityRepositoryProvider::class)
            ->arg(0, new Reference('doctrine'))
            ->public()

        ->set(EntityFactory::class)
            ->arg(0, new Reference(EntityPresenter::class))
            ->arg(1, new Reference('dgtx.entity.repository.provider'))

        ->set(ContextListener::class)
            ->arg(0, new Reference(ContextFactory::class))
            ->arg(1, new Reference(EntityFactory::class))
            ->arg(2, new Reference('dgtx.entity.repository.provider'))
            ->tag('kernel.event_listener', ['event' => ControllerEvent::class])
        ->alias(ContextListener::class, 'dgtx.listener.context')

        ->set(ContextProvider::class)
            ->arg(0, new Reference('request_stack'))

        ->set(ControllerListener::class)
            ->arg(0, new Reference('twig'))
            ->arg(1, new Reference(ContextProvider::class))
            ->tag('kernel.event_listener', ['event' => ControllerEvent::class])

        ->set('dgtx.entity.repository', EntityRepository::class)
            ->arg(0, new Reference('doctrine'))
            ->arg(1, new Reference(ContextProvider::class, ContainerInterface::NULL_ON_INVALID_REFERENCE))
            ->arg(2, new Reference('', ContainerInterface::NULL_ON_INVALID_REFERENCE))
            ->tag('doctrine.repository_service')
            ->public()

        ->set('dgtx.helper.view', HelperView::class)
            ->arg(0, new Reference(ContextProvider::class))

        ->set('dgtx.helper.view.factory', HelperViewFactory::class)
            ->arg(0, new Reference('dgtx.helper.view'))
            ->public()

        ->set('dgtx.sorter.factory', SorterFactory::class)
            ->arg(0, new Reference(SorterInterface::class))
            ->public()

        ->set('dgtx.sorter', Sorter::class)
            ->arg(0, new Reference(ContextProvider::class))
            ->arg(1, new Reference('router.default'))

        ->alias(SorterInterface::class, 'dgtx.sorter')

        ->set('dgtx.paginator', Paginator::class)

        ->set('dgtx.filter.factory', FilterFactory::class)
            ->arg(0, new Reference(DigitixFrameworkExtension::ALIAS_ENTITY_CONFIG))
            ->arg(1, new Reference(ContextProvider::class))
            ->public()

        ->set('dgtx.form.factory', FormFactory::class)
            ->arg(0, new Reference(FormFactoryInterface::class))
            ->arg(1, new Reference(ContextProvider::class))
            ->public()

        ->set('dgtx.search.factory', SearchFactory::class)
            ->public()

        ->set('dgtx.paginator.factory', PaginatorFactory::class)
            ->arg(0, new Reference(ContextProvider::class))
            ->arg(1, new Reference('router.default'))
            ->public()

        ->set('dgtx.helper.list.factory', HelperListFactory::class)
            ->arg(0, new Reference('dgtx.helper.list'))
            ->public()

        ->set('dgtx.helper.list', HelperList::class)
            ->arg(0, new Reference(ContextProvider::class))

        ->alias(HelperListInterface::class, 'dgtx.helper.list')

        ->set('dgtx.field.factory', FieldFactory::class)
            ->public()

        ->set('dgtx.helper.form', HelperForm::class)
            ->arg(0, new Reference(ContextProvider::class))

        ->alias(HelperFormInterface::class,'dgtx.helper.form')

        ->set('dgtx.helper.form.factory', HelperFormFactory::class)
            ->arg(0, new Reference('dgtx.helper.form'))
            ->public()

        ->set('dgtx.mailer', Mailer::class)
            ->arg(0, new Reference('mailer.mailer'))
            ->arg(0, new Reference(ConfigurationProvider::class))
            ->arg(0, new Reference(ContextProvider::class))

        ->set('dgtx.entity.persister', EntityPersister::class)
            ->arg(0, new Reference('doctrine'))
            ->arg(1, new Reference(ValidatorInterface::class))
            ->public()

        ->set('dgtx.translation.finder', TranslationFinder::class)
            ->arg(0, '%kernel.project_dir%')
            ->public()

        ->set('dgtx.translation.provider', TranslationProvider::class)
            ->public()

        ->set('dgtx.translation.form.factory', TranslationFormFactory::class)
            ->arg(0, new Reference('dgtx.translation.finder'))
            ->arg(1, new Reference('dgtx.translation.provider'))
            ->public()

        ->set('dgtx.translation.updater', TranslationUpdater::class)
            ->arg(0, new Reference('translation.writer'))
            ->arg(1, '%kernel.project_dir%')
            ->public()

        ->set(PasswordType::class)
            ->arg(0, new Reference('security.user_password_encoder.generic'))
            ->arg(1, new Reference(ContextProvider::class))
            ->tag('form.type')

        ->set('dgtx.configuration.provider', ConfigurationProvider::class)
            ->arg(0, new Reference('dgtx.entity.repository.provider'))
            ->public()

        ->set('dgtx.cache', Cache::class)
            ->arg(0, new Reference('kernel'))
            ->public()

        ->set(UserFixtures::class)
            ->arg(0, new Reference('security.user_password_encoder.generic'))
            ->public()
            ->tag('doctrine.fixture.orm')
        
        ->set(LanguageFixtures::class)
            ->public()
            ->tag('doctrine.fixture.orm')
    ;
};
