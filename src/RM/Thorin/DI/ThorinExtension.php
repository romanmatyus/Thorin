<?php

namespace RM\Thorin\DI;

use Nette\Object;
use Nette\DI\CompilerExtension;
use RM\Thorin\IMode;
use RM\Thorin\IRouter;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\Modes\BaseMode;

/**
 * Extension for registration Thorin to Nette application.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class ThorinExtension extends CompilerExtension
{
	/** @var [] */
	private $defaults = [
		"modes" => NULL,
		"defaultMode" => NULL,
	];


	public function loadConfiguration()
	{
		$this->validateConfig($this->defaults);

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('router'))
			->setFactory('RM\Thorin\Routers\SimpleRouter', [
				'/webtemp',
			]);

		$builder->addDefinition($this->prefix('defaultStorage'))
			->setFactory('RM\Thorin\ModeStorages\DefaultStorage');

		$builder->addDefinition($this->prefix('modeGenerator'))
			->setFactory('RM\Thorin\ModeGenerator', ['@' . $this->prefix('defaultMode')]);

		$dirs = [];
		if (isset($builder->parameters['wwwDir']))
			$dirs[] = $builder->parameters['wwwDir'] . '/';
		if (isset($builder->parameters['appDir']))
			$dirs[] = $builder->parameters['appDir'] . '/';

		$builder->addDefinition($this->prefix('pathProvider'))
			->setFactory('RM\Thorin\Providers\PathProvider', [
				'dirs' => array_unique(array_filter($dirs)),
			]);
		$builder->addDefinition($this->prefix('returnProvider'))
			->setFactory('RM\Thorin\Providers\ReturnProvider');

		$builder->addDefinition($this->prefix('webTempGenerator'))
			->setFactory('RM\Thorin\Generators\WebTempGenerator', [
				'destinationPath' => (isset($builder->parameters['wwwDir']) ? $builder->parameters['wwwDir'] : '') . '/webtemp',
			]);

		$builder->addDefinition($this->prefix('defaultMode'))
			->setFactory('RM\Thorin\Modes\DefaultMode');

		$thorinDef = $builder->addDefinition($this->prefix('thorin'))
			->setFactory('RM\Thorin\Thorin', [
				$this->prefix('@defaultMode'),
			]);

		if (is_array($this->config['modes'])) {
			foreach ($this->config['modes'] as $name => $mode) {
				if (!isset($mode['name']))
					$mode['name'] = $name;
				$thorinDef->addSetup('createMode', [$mode]);
			}
		}

		$builder->addDefinition($this->prefix('filters'))
			->setFactory('RM\Thorin\Latte\Runtime\ThorinFilters');

		$builder->addDefinition($this->prefix('presenter'))
			->setFactory('RM\Thorin\Application\UI\ThorinPresenter');

	}


	function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$thorinDef = $builder->getDefinition($this->prefix('thorin'));

		foreach ($builder->findByType(IMode::class) as $mode)
			$thorinDef->addSetup('addModeOnlyNew', [$mode]);


		if ($this->config['defaultMode'] !== NULL) {
			if (substr($this->config['defaultMode'], 0, 1) === '@') {
				$thorinDef->addSetup('setDefaultMode', [$this->config['defaultMode']]);
			} elseif (strlen($this->config['defaultMode']) > 0) {
				$thorinDef->addSetup('$service->setDefaultMode($service->getMode(?))', [$this->config['defaultMode']]);
			}
		} else {
			$thorinDef->addSetup('setDefaultMode', ['@' . $this->prefix('defaultMode')]);
		}

		if ($builder->hasDefinition('routing.router')) {
			$netteRouter = $builder->getDefinition('routing.router');
			foreach ($builder->findByType(IRouter::class) as $name => $router) {
				// Bug https://github.com/nette/di/issues/66
				if ($name === $this->prefix('router') && count($builder->findByType(IRouter::class))>1) {
					$builder->removeDefinition($this->prefix('router'));
					continue;
				}
				$thorinDef->addSetup('addRouter', [$router]);

				$netteRouter->addSetup('
		$outputPath = $this->getService(?)->getOutputPath();
		if (substr($outputPath, 0, 1) === \'/\')
			$outputPath = substr($outputPath, 1);
		$service[] = new Nette\Application\Routers\Route(\'<link \' . $outputPath . \'.+>\', ?, Nette\Application\Routers\Route::ONE_WAY);', [$name, 'Thorin:Thorin:render']);
			}
		}

		if ($builder->hasDefinition('nette.latteFactory')) {
			$builder->getDefinition('nette.latteFactory')
				->addSetup('addFilter', ['mode', [$this->prefix('@filters'), 'mode']])
				->addSetup('addFilter', ['image', [$this->prefix('@filters'), 'image']])
				->addSetup('addFilter', ['resize', [$this->prefix('@filters'), 'resize']]);
		}

		if ($builder->hasDefinition('nette.presenterFactory')) {
			$builder->getDefinition('nette.presenterFactory')
				->addSetup('setMapping', [
					['Thorin' => 'RM\Thorin\Application\UI\*Presenter'],
				]);
		}
	}

}
