<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Armazenamento simples de configurações (chave/valor) da plataforma.
 * Valores sensíveis (ex.: chave de API) são guardados criptografados.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'is_encrypted'];

    protected $casts = ['is_encrypted' => 'boolean'];

    /** Lê uma configuração (decifra se for criptografada). */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('key', $key)->first();

        if (!$row || $row->value === null || $row->value === '') {
            return $default;
        }

        if ($row->is_encrypted) {
            try {
                return Crypt::decryptString($row->value);
            } catch (Throwable) {
                return $default;
            }
        }

        return $row->value;
    }

    /** Grava uma configuração (criptografa se solicitado). */
    public static function put(string $key, ?string $value, bool $encrypt = false): void
    {
        $stored = ($encrypt && $value !== null && $value !== '')
            ? Crypt::encryptString($value)
            : $value;

        static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'is_encrypted' => $encrypt]
        );
    }

    /** Existe um valor não-vazio para a chave? */
    public static function has(string $key): bool
    {
        $row = static::query()->where('key', $key)->first();
        return $row && $row->value !== null && $row->value !== '';
    }
}
