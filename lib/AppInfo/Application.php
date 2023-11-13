<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Ano Rangga Rahardika <rahardikaku@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\FilesWm\AppInfo;

use OCA\MyApp\Event\AddEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\EventDispatcher\IEventDispatcher;
use OCA\Files\Event\LoadAdditionalScriptsEvent;

class Application extends App implements IBootstrap {
	public const APP_ID = 'fileswm';

	public function __construct() {
		parent::__construct(self::APP_ID);
		$dispatcher = $this->getContainer()->query(IEventDispatcher::class);
		$dispatcher->addListener(LoadAdditionalScriptsEvent::class, function() {
			// ...
			\OCP\Util::addScript(self::APP_ID, 'fileswm-main' );
		});
	}

	 public function register(IRegistrationContext $context): void {
        // ... registration logic goes here ...

        // Register the composer autoloader for packages shipped by this app, if applicable
        include_once __DIR__ . '/../../vendor/autoload.php';

        // $context->registerEventListener(
        //     BeforeUserDeletedEvent::class,
        //     UserDeletedListener::class
        // );
    }

	public function boot(IBootContext $context): void {
        // ... boot logic goes here ...

        /** @var IManager $manager */
        // $manager = $context->getAppContainer()->query(IManager::class);
        // $manager->registerNotifierService(Notifier::class);
    }
}
