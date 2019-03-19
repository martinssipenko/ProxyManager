<?php

declare(strict_types=1);

namespace ProxyManager\Inflector;

use ProxyManager\Inflector\Util\ParameterHasher;
use function ltrim;
use function strlen;
use function strrpos;
use function substr;

/**
 * {@inheritDoc}
 */
final class ClassNameInflector implements ClassNameInflectorInterface
{
    /** @var string */
    protected $proxyNamespace;

    /** @var int */
    private $proxyMarkerLength;

    /** @var string */
    private $proxyMarker;

    /** @var ParameterHasher */
    private $parameterHasher;

    public function __construct(string $proxyNamespace)
    {
        $this->proxyNamespace    = $proxyNamespace;
        $this->proxyMarker       = '\\' . self::PROXY_MARKER . '\\';
        $this->proxyMarkerLength = strlen($this->proxyMarker);
        $this->parameterHasher   = new ParameterHasher();
    }

    /**
     * {@inheritDoc}
     */
    public function getUserClassName(string $className) : string
    {
        $className = ltrim($className, '\\');
        $position  = strrpos($className, $this->proxyMarker);

        if ($position === false) {
            return $className;
        }

        return substr(
            $className,
            $this->proxyMarkerLength + $position,
            strrpos($className, '\\') - ($position + $this->proxyMarkerLength)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyClassName(string $className, array $options = []) : string
    {
        return $this->proxyNamespace
            . $this->proxyMarker
            . $this->getUserClassName($className)
            . '\\Generated' . $this->parameterHasher->hashParameters($options);
    }

    /**
     * {@inheritDoc}
     */
    public function isProxyClassName(string $className) : bool
    {
        return strrpos($className, $this->proxyMarker) !== false;
    }
}
