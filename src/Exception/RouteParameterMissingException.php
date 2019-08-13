<?php
declare(strict_types=1);

namespace GimmeUrl\Exception;

use Exception;
use GimmeUrl\RouteMetadata;

final class RouteParameterMissingException extends Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function forRoute(RouteMetadata $metadata, string $parameter): self
    {
        if (empty($metadata->getName())) {
            return new static(sprintf(
                'Route parameter "%s" is missing for path "%s".',
                $parameter,
                $metadata->getPath()
            ));
        }

        return new static(sprintf(
            'Parameter "%s" is missing for named route "%s".',
            $parameter,
            $metadata->getName()
        ));
    }
}
