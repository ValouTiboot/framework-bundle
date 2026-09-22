<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Menu;

/**
 * The tree posted by the menu builder is not acceptable (unknown parent,
 * too deep, missing URL...). The message is meant to be shown to the user.
 */
final class MenuTreeException extends \InvalidArgumentException
{
}
