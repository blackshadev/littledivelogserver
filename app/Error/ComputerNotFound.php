<?php

declare(strict_types=1);

namespace App\Error;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ComputerNotFound extends NotFoundHttpException
{
}
