<?php

use Nette\Configurator;
use RM\Thorin\Application\UI\ThorinPresenter;
use RM\Thorin\Descriptor;
use RM\Thorin\IMode;
use RM\Thorin\Routers\SimpleRouter;
use RM\Thorin\Thorin;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$configurator = new Configurator;

@mkdir(__DIR__."/temp", 0777);
@mkdir(__DIR__."/webtemp", 0777);

$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/config.neon');
$configurator->setDebugMode(TRUE);
$container = $configurator->createContainer();

$thorin = $container->getByType(Thorin::class);
Assert::type(Thorin::class, $thorin);

Assert::type(IMode::class, $thorin->getMode('default'));
Assert::type(IMode::class, $thorin->getMode('resize'));
Assert::type(IMode::class, $thorin->getMode('article'));
Assert::type(IMode::class, $thorin->getMode('userMode'));

$simpleRouter = $container->getByType(SimpleRouter::class);
Assert::type(SimpleRouter::class, $simpleRouter);
Assert::same('/webtemp', $simpleRouter->getOutputPath());

$latte = $container->getByType(Nette\Bridges\ApplicationLatte\ILatteFactory::class)->create();
Assert::type(Descriptor::class, $latte->invokeFilter('mode', ['image.png', 'resize']));

$router = $container->getByType(Nette\Application\IRouter::class);

$url = new Nette\Http\UrlScript('http://example.com/webtemp/image/d');
$request = new Nette\Http\Request($url);
$appRequest = $router->match($request);
Assert::type(Nette\Application\Request::class, $appRequest);

Assert::same('Thorin:Thorin', $appRequest->getPresenterName());
Assert::same('render', $appRequest->getParameter('action'));
Assert::same('webtemp/image/d', $appRequest->getParameter('link'));

$presenter = $container->getByType(Nette\Application\IPresenterFactory::class)->createPresenter($appRequest->getPresenterName());
Assert::type(ThorinPresenter::class, $presenter);

Assert::exception(function () use ($presenter, $appRequest) {
	$response = $presenter->run($appRequest);
}, 'Nette\Application\BadRequestException', "Link 'webtemp/image/d' is invalid. Source not found.");

$url = new Nette\Http\UrlScript('http://example.com' . $thorin->getLinkFromSource('../assets/nette-logo-blue.png'));
$request = new Nette\Http\Request($url);
$appRequest = $router->match($request);
ob_start();
$presenter->run($appRequest);
$response = ob_get_contents();
$image = Nette\Utils\Image::fromString($response);
Assert::same(600, $image->getWidth());
