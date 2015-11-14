<?php

namespace RM\Thorin;

interface Exception
{
}

class InvalidArgumentException extends \InvalidArgumentException implements Exception
{
}

class FileNotFoundException extends \Nette\FileNotFoundException implements Exception
{
}
