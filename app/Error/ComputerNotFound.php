<?php

declare(strict_types=1);

namespace App\Error;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ComputerNotFound extends NotFoundHttpException
{
}
