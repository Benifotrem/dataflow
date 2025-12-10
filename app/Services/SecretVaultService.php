<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Sistema de bóveda de secretos con encriptación AES-256
 * Nivel de seguridad: MILITAR
 */
class SecretVaultService
{
    protected string $table = 'secret_vault';
    
    /**
     * Almacenar secreto encriptado
     */
    public function store(string $key, string $value, array $metadata = []): bool
    {
        try {
            // Encriptar el valor con AES-256-CBC
            $encrypted = Crypt::encryptString($value);
            
            // Hash del valor para verificación de integridad
            $hash = hash('sha256', $value);
            
            // Registrar en auditoría
            $this->auditAccess($key, 'STORE');
            
            DB::table($this->table)->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $encrypted,
                    'hash' => $hash,
                    'metadata' => json_encode($metadata),
                    'last_rotated_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
            
            Log::info('Secreto almacenado de forma segura', [
                'key' => $key,
                'metadata' => $metadata,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::critical('ERROR CRÍTICO: Fallo al almacenar secreto', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * Recuperar secreto desencriptado
     */
    public function get(string $key): ?string
    {
        try {
            // Registrar acceso para auditoría
            $this->auditAccess($key, 'READ');
            
            $record = DB::table($this->table)
                ->where('key', $key)
                ->first();
            
            if (!$record) {
                Log::warning('Intento de acceso a secreto inexistente', ['key' => $key]);
                return null;
            }
            
            // Desencriptar
            $decrypted = Crypt::decryptString($record->value);
            
            // Verificar integridad
            $currentHash = hash('sha256', $decrypted);
            if ($currentHash !== $record->hash) {
                Log::critical('ALERTA DE SEGURIDAD: Hash de secreto no coincide - posible manipulación', [
                    'key' => $key,
                ]);
                throw new \Exception('Integridad del secreto comprometida');
            }
            
            return $decrypted;
            
        } catch (\Exception $e) {
            Log::error('Error al recuperar secreto', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }
    
    /**
     * Eliminar secreto permanentemente
     */
    public function delete(string $key): bool
    {
        $this->auditAccess($key, 'DELETE');
        
        return DB::table($this->table)
            ->where('key', $key)
            ->delete() > 0;
    }
    
    /**
     * Rotar secreto (cambiar por uno nuevo)
     */
    public function rotate(string $key, string $newValue): bool
    {
        $this->auditAccess($key, 'ROTATE');
        
        return $this->store($key, $newValue, [
            'rotated' => true,
            'rotation_date' => Carbon::now()->toDateTimeString(),
        ]);
    }
    
    /**
     * Listar todos los secretos (sin valores)
     */
    public function list(): array
    {
        return DB::table($this->table)
            ->select('key', 'last_rotated_at', 'updated_at')
            ->get()
            ->toArray();
    }
    
    /**
     * Verificar si un secreto necesita rotación (>90 días)
     */
    public function needsRotation(string $key): bool
    {
        $record = DB::table($this->table)
            ->where('key', $key)
            ->first();
        
        if (!$record || !$record->last_rotated_at) {
            return true;
        }
        
        $rotationDate = Carbon::parse($record->last_rotated_at);
        return $rotationDate->diffInDays(Carbon::now()) > 90;
    }
    
    /**
     * Auditoría de accesos a secretos
     */
    protected function auditAccess(string $key, string $action): void
    {
        try {
            DB::table('secret_audit_log')->insert([
                'secret_key' => $key,
                'action' => $action,
                'ip_address' => request()->ip() ?? 'CLI',
                'user_agent' => request()->userAgent() ?? 'CLI',
                'timestamp' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            // No fallar si la auditoría falla
            Log::warning('Fallo en auditoría de secretos', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Obtener auditoría de un secreto
     */
    public function getAuditLog(string $key, int $limit = 100): array
    {
        return DB::table('secret_audit_log')
            ->where('secret_key', $key)
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
    
    /**
     * Detectar accesos sospechosos
     */
    public function detectSuspiciousActivity(): array
    {
        $suspicious = [];
        
        // Detectar múltiples accesos desde IPs diferentes
        $multipleIPs = DB::table('secret_audit_log')
            ->select('secret_key', DB::raw('COUNT(DISTINCT ip_address) as ip_count'))
            ->where('timestamp', '>', Carbon::now()->subHours(1))
            ->groupBy('secret_key')
            ->having('ip_count', '>', 3)
            ->get();
        
        foreach ($multipleIPs as $item) {
            $suspicious[] = [
                'key' => $item->secret_key,
                'reason' => 'Múltiples IPs diferentes en 1 hora',
                'ip_count' => $item->ip_count,
            ];
        }
        
        // Detectar accesos excesivos
        $excessiveAccess = DB::table('secret_audit_log')
            ->select('secret_key', DB::raw('COUNT(*) as access_count'))
            ->where('timestamp', '>', Carbon::now()->subMinutes(10))
            ->groupBy('secret_key')
            ->having('access_count', '>', 50)
            ->get();
        
        foreach ($excessiveAccess as $item) {
            $suspicious[] = [
                'key' => $item->secret_key,
                'reason' => 'Accesos excesivos en 10 minutos',
                'access_count' => $item->access_count,
            ];
        }
        
        return $suspicious;
    }
}
