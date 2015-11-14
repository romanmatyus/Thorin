<?php

namespace RM\Thorin\Application\UI;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use RM\Thorin\Descriptor;
use RM\Thorin\Thorin;

/**
 * NettePresenter for lazy generating thumbnails from sources.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class ThorinPresenter extends Presenter
{
	/** @var Thorin @inject */
	public $thorin;

	public function actionRender($link)
	{
		$descriptor = $this->thorin->getDescriptorFromLink($link);
		if (!$descriptor instanceof Descriptor)
			throw new BadRequestException("Link '$link' is invalid. Source not found.");
		$descriptor->getImage()->send();
		$this->terminate();
	}
}
