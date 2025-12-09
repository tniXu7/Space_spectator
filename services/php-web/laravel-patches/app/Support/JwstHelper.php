<?php

namespace App\Support;

final class JwstHelper
{
    private string $host;
    private string $key;
    private ?string $email;

    public function __construct()
    {
        $this->host  = rtrim(getenv('JWST_HOST') ?: 'https://api.jwstapi.com', '/');
        $this->key   = getenv('JWST_API_KEY') ?: '';
        $this->email = getenv('JWST_EMAIL') ?: null;
    }

    public function get(string $path, array $qs = []): array
    {
        $url = $this->host.'/'.ltrim($path, '/');
        if ($qs) $url .= (str_contains($url,'?')?'&':'?').http_build_query($qs);
        
        // JWST API требует правильный формат авторизации
        // Используем x-api-key в заголовке, но также пробуем Authorization если нужно
        $headers = [
            'x-api-key: '.$this->key,
            'Content-Type: application/json',
        ];
        if ($this->email) {
            $headers[] = 'email: '.$this->email;
        }
        
        // Если API требует Basic auth, используем его
        if (!empty($this->key) && !empty($this->email)) {
            // Некоторые версии API требуют Basic auth с email:key
            $auth = base64_encode($this->email . ':' . $this->key);
            $headers[] = 'Authorization: Basic ' . $auth;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Если получили ошибку, логируем и возвращаем пустой массив
        if ($httpCode >= 400 || $raw === false) {
            error_log("JWST API Error: HTTP $httpCode - " . ($error ?: substr($raw, 0, 200)));
            return [];
        }

        $j = json_decode((string)$raw, true);
        return is_array($j) ? $j : [];
    }

    /** ищем первую пригодную картинку в произвольной структуре */
    public static function pickImageUrl(array $v): ?string
    {
        $stack = [$v];
        while ($stack) {
            $cur = array_pop($stack);
            foreach ($cur as $k => $val) {
                if (is_string($val) && preg_match('~^https?://.*\.(?:jpg|jpeg|png)$~i', $val)) return $val;
                if (is_array($val)) $stack[] = $val;
            }
        }
        return null;
    }
}
