<?php

namespace App\Workers;

/**
 * Worker para validación de contenido
 *
 * Maneja la detección de palabras ofensivas y patrones de ataque
 */
class ContentValidationWorker
{
    /**
     * Patrones de palabras ofensivas
     *
     * @var array
     */
    protected array $offensiveWords = [
        '/maric[oó]n(es)?\b/ui',
        '/imb[eé]cil(es)?\b/ui',
        '/idiota(s)?\b/ui',
        '/subnormal(es)?\b/ui',
        '/retrasad[oa]s?\b/ui',
        '/gilipollas\b/ui',
        '/capullo(s)?\b/ui',
        '/cabr[oó]n(es)?\b/ui',
        '/pendejo(s)?\b/ui',
        '/est[uú]pido(s)?\b/ui',
        '/bastardo(s)?\b/ui',
        '/zorra(s)?\b/ui',
        '/puta(s)?\b/ui',
        '/put[oó]n(es)?\b/ui',
        '/mam[oó]n(es)?\b/ui',
        '/pringado(s)?\b/ui',
        '/payaso(s)?\b/ui',
        '/anormal(es)?\b/ui',
        '/cerdo(s)?\b/ui',
        '/asqueroso(s)?\b/ui',
        '/mierda\b/ui',
        '/cojones\b/ui',
        '/joder\b/ui',
        '/hostia(s)?\b/ui',
        '/lerdo(s)?\b/ui',
        '/memo(s)?\b/ui',
        '/tonto(s)?\b/ui',
        '/in[uú]til(es)?\b/ui',
        '/baboso(s)?\b/ui',
        '/cutre(s)?\b/ui',
        '/pat[eé]tico(s)?\b/ui',
        '/rastrero(s)?\b/ui',
        '/sinverg[uü]enza(s)?\b/ui',
        '/come\s*mierda(s)?\b/ui',
        '/come\s*pollas\b/ui',
        '/chupa\s*pollas\b/ui',
        '/cara\s*de\s*culo\b/ui',
        '/cara\s*de\s*puta\b/ui',
        '/vete\s*a\s*la\s*mierda\b/ui',
        '/me\s*cago\s*en\s*(dios|la\s*leche|tu\s*madre)\b/ui',
        '/hijo\s*de\s*puta(s)?\b/ui',
        '/tu\s*puta\s*madre\b/ui',
        '/malnacid[oa]s?\b/ui',
        '/escoria\b/ui',
        '/basura\b/ui',
        '/p[u*]ta\b/ui',
        '/m[i1]erda\b/ui',
        '/c[a@]br[o0]n\b/ui',
        '/gil[i1]pollas\b/ui',
        '/imb[e3]cil\b/ui',
        '/idi[o0]ta\b/ui',
        '/fuck(er|ing|ed)?\b/ui',
        '/mother\s*fucker(s)?\b/ui',
        '/son\s*of\s*a\s*bitch\b/ui',
        '/bitch(es)?\b/ui',
        '/asshole(s)?\b/ui',
        '/dick(head)?s?\b/ui',
        '/jerk(s)?\b/ui',
        '/bastard(s)?\b/ui',
        '/shit(head)?s?\b/ui',
        '/bullshit\b/ui',
        '/cunt(s)?\b/ui',
        '/slut(s)?\b/ui',
        '/whore(s)?\b/ui',
        '/retard(ed)?\b/ui',
        '/moron(s)?\b/ui',
        '/loser(s)?\b/ui',
        '/scumbag(s)?\b/ui',
        '/dumbass(es)?\b/ui',
        '/jackass(es)?\b/ui',
    ];

    /**
     * Patrones de ataques (XSS, SQL Injection, etc.)
     *
     * @var array
     */
    protected array $dangerousPatterns = [
        '/<\s*\/?\s*script\b/i',
        '/<\s*(iframe|object|embed|form|svg|math|link|meta|style)\b/i',
        '/on\w+\s*=/i',
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/data\s*:/i',
        '/expression\s*\(/i',
        '/document\.(cookie|write|location)/i',
        '/window\.(open|location|eval)/i',
        '/alert\s*\(|confirm\s*\(|prompt\s*\(/i',
        '/&#x?[0-9a-fA-F]+;/',
        '/&#0*[0-9]+;/',
        '/\\\\x[0-9a-fA-F]{2}/',
        '/%[0-9a-fA-F]{2}/',
        '/\\\\u[0-9a-fA-F]{4}/',
        '/\x00/',
        '/\b(select|insert|update|delete|drop|truncate|replace)\b/i',
        '/\bunion\b.*\bselect\b/i',
        '/\bor\s+1\s*=\s*1\b/i',
        '/--|#|\/\*/',
        '/;\s*(select|insert|update|delete|drop)/i',
        '/`[^`]+`/',
        '/\$\([^)]+\)/',
        '/\|\||&&|;/',
        '/\b(cat|ls|id|whoami|uname|wget|curl|nc)\b/i',
        '/\.\.\//',
        '/\.\.\\\\/',
        '/\/etc\/passwd/i',
        '/windows\\\\system32/i',
        '/\beval\s*\(/i',
        '/\bexec\s*\(/i',
        '/\bsystem\s*\(/i',
        '/\bshell_exec\s*\(/i',
        '/\bbase64_decode\s*\(/i',
    ];

    /**
     * Validar contenido del mensaje
     *
     * @param string $text Texto del mensaje
     * @param string $title Título del mensaje
     * @return string 'false' | 'words' | 'attack'
     */
    public function validate(string $text, string $title): string
    {
        $textLower = mb_strtolower(trim($text), 'UTF-8');
        $titleLower = mb_strtolower(trim($title), 'UTF-8');

        // Verificar palabras ofensivas
        foreach ($this->offensiveWords as $pattern) {
            if (preg_match($pattern, $textLower) || preg_match($pattern, $titleLower)) {
                return 'words';
            }
        }

        // Verificar patrones peligrosos
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $textLower) || preg_match($pattern, $titleLower)) {
                return 'attack';
            }
        }

        return 'false';
    }

    /**
     * Verificar si el contenido tiene palabras ofensivas
     *
     * @param string $text Texto a verificar
     * @param string $title Título a verificar
     * @return bool
     */
    public function hasOffensiveWords(string $text, string $title): bool
    {
        return $this->validate($text, $title) === 'words';
    }

    /**
     * Verificar si el contenido tiene patrones de ataque
     *
     * @param string $text Texto a verificar
     * @param string $title Título a verificar
     * @return bool
     */
    public function hasDangerousPatterns(string $text, string $title): bool
    {
        return $this->validate($text, $title) === 'attack';
    }
}
