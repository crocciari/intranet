<?php

    // ============================================
    // FUNÇÕES DE API
    // ============================================
    function get_posts_as_array($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("❌ URL inválida");
        }

        $json_data = @file_get_contents($url);
        if ($json_data === false) {
            throw new Exception("❌ Falha ao acessar o endpoint. Verifique a URL e permissões.");
        }

        $data = json_decode($json_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("❌ JSON inválido: " . json_last_error_msg());
        }

        return $data;
    }

    // ============================================
    // FUNÇÕES DE SEGURANÇA
    // ============================================
    const CONF_PASSWD_MIN_LEN = 8;
    const CONF_PASSWD_MAX_LEN = 40;
    const CONF_PASSWD_ALGO = PASSWORD_DEFAULT;
    const CONF_PASSWD_OPTION = ["cost" => 10];

    function passwd(string $password): string {
        if (!empty(password_get_info($password)['algo'])) {
            return $password;
        }
        return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
    }

    function passwd_verify(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    function passwd_rehash(string $hash): bool {
        return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
    }

    function gerarStringPersonalizada($quantidade, $incluirMaiusculas = true, $incluirMinusculas = true, $incluirNumeros = true, $incluirSimbolos = true) {
        $maiusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $minusculas = 'abcdefghijklmnopqrstuvwxyz';
        $numeros = '0123456789';
        $simbolos = '!@#$%&*()-_=+[]{}<>;:,./?';
        
        $caracteres = '';
        if ($incluirMaiusculas) $caracteres .= $maiusculas;
        if ($incluirMinusculas) $caracteres .= $minusculas;
        if ($incluirNumeros) $caracteres .= $numeros;
        if ($incluirSimbolos) $caracteres .= $simbolos;
        
        if (empty($caracteres)) {
            $caracteres = $maiusculas . $minusculas . $numeros . $simbolos;
        }
        
        $tamanhoCaracteres = strlen($caracteres);
        $resultado = '';
        
        for ($i = 0; $i < $quantidade; $i++) {
            $resultado .= $caracteres[random_int(0, $tamanhoCaracteres - 1)];
        }
        
        return $resultado;
    }

    // ============================================
    // GERADOR DE UUID
    // ============================================
    function gerarUUID() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    ?>
