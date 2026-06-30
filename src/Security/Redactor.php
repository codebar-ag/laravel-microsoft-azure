<?php

namespace CodebarAg\MicrosoftAzure\Security;

/**
 * Structural redaction of secrets before anything is logged, evented, or thrown.
 */
final class Redactor
{
    public const PLACEHOLDER = '[REDACTED]';

    /**
     * @var list<string>
     */
    private const SECRET_KEYS = [
        'authorization',
        'password',
        'pwd',
        'token',
        'access_token',
        'refresh_token',
        'id_token',
        'client_secret',
        'cookie',
        'set-cookie',
    ];

    public function redact(mixed $value): mixed
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $key => $item) {
                $out[$key] = $this->isSecretKey($key)
                    ? self::PLACEHOLDER
                    : $this->redact($item);
            }

            return $out;
        }

        if (is_string($value)) {
            return $this->string($value);
        }

        return $value;
    }

    public function string(string $value): string
    {
        $value = (string) preg_replace('/\bBearer\s+[A-Za-z0-9\-._~+\/]+=*/i', 'Bearer '.self::PLACEHOLDER, $value);

        $keys = implode('|', array_map('preg_quote', self::SECRET_KEYS));
        $value = (string) preg_replace('/("(?:'.$keys.')"\s*:\s*")[^"]*(")/i', '$1'.self::PLACEHOLDER.'$2', $value);
        $value = (string) preg_replace('/\b('.$keys.')=([^&\s]+)/i', '$1='.self::PLACEHOLDER, $value);

        return $value;
    }

    private function isSecretKey(int|string $key): bool
    {
        return is_string($key) && in_array(strtolower($key), self::SECRET_KEYS, true);
    }
}
