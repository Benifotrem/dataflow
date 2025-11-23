<!DOCTYPE html>
<html>
<head>
    <title>Contaplus - Diagnóstico</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #667eea; }
        .section { margin: 20px 0; padding: 15px; background: #f9fafb; border-left: 4px solid #667eea; }
        .success { color: #059669; }
        .error { color: #dc2626; }
        .info { color: #0284c7; }
        pre { background: #1f2937; color: #f3f4f6; padding: 10px; border-radius: 4px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        td:first-child { font-weight: bold; width: 200px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico de Contaplus</h1>

        <div class="section">
            <h2>1. Información del Servidor</h2>
            <table>
                <tr><td>PHP Version</td><td><?php echo phpversion(); ?></td></tr>
                <tr><td>Document Root</td><td><?php echo $_SERVER['DOCUMENT_ROOT']; ?></td></tr>
                <tr><td>Script Filename</td><td><?php echo $_SERVER['SCRIPT_FILENAME']; ?></td></tr>
                <tr><td>HTTP Host</td><td><?php echo $_SERVER['HTTP_HOST'] ?? 'N/A'; ?></td></tr>
                <tr><td>Request URI</td><td><?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?></td></tr>
                <tr><td>Server Software</td><td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></td></tr>
            </table>
        </div>

        <div class="section">
            <h2>2. Estructura de Archivos Laravel</h2>
            <?php
            $projectRoot = dirname(__DIR__);
            $requiredPaths = [
                'bootstrap/app.php' => 'Bootstrap',
                'app/Http/Controllers' => 'Controllers',
                'routes/web.php' => 'Routes',
                'resources/views' => 'Views',
                '.env' => 'Environment Config',
                'public/index.php' => 'Front Controller',
                'public/.htaccess' => 'Rewrite Rules',
            ];

            echo "<table>";
            foreach ($requiredPaths as $path => $name) {
                $fullPath = $projectRoot . '/' . $path;
                $exists = file_exists($fullPath);
                $status = $exists ? '<span class="success">✓ Existe</span>' : '<span class="error">✗ No encontrado</span>';
                echo "<tr><td>$name</td><td>$status<br><small style='color: #6b7280;'>$fullPath</small></td></tr>";
            }
            echo "</table>";
            ?>
        </div>

        <div class="section">
            <h2>3. Permisos de Archivos</h2>
            <?php
            $checkPaths = [
                'storage',
                'storage/logs',
                'storage/framework/cache',
                'storage/framework/sessions',
                'storage/framework/views',
                'bootstrap/cache',
            ];

            echo "<table>";
            foreach ($checkPaths as $path) {
                $fullPath = $projectRoot . '/' . $path;
                if (file_exists($fullPath)) {
                    $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
                    $writable = is_writable($fullPath);
                    $status = $writable ? '<span class="success">✓ Escribible (' . $perms . ')</span>' : '<span class="error">✗ No escribible (' . $perms . ')</span>';
                } else {
                    $status = '<span class="error">✗ No existe</span>';
                }
                echo "<tr><td>$path</td><td>$status</td></tr>";
            }
            echo "</table>";
            ?>
        </div>

        <div class="section">
            <h2>4. Variables de Entorno (.env)</h2>
            <?php
            $envPath = $projectRoot . '/.env';
            if (file_exists($envPath)) {
                echo '<p class="success">✓ Archivo .env encontrado</p>';
                $envContent = file_get_contents($envPath);
                $envLines = explode("\n", $envContent);
                $importantVars = ['APP_ENV', 'APP_URL', 'APP_DEBUG', 'DB_CONNECTION', 'DB_DATABASE'];

                echo "<table>";
                foreach ($envLines as $line) {
                    $line = trim($line);
                    if (empty($line) || $line[0] === '#') continue;

                    foreach ($importantVars as $var) {
                        if (strpos($line, $var . '=') === 0) {
                            $parts = explode('=', $line, 2);
                            $value = $parts[1] ?? '';
                            // Hide sensitive data
                            if (strpos($var, 'PASSWORD') !== false || strpos($var, 'SECRET') !== false) {
                                $value = '***';
                            }
                            echo "<tr><td>$var</td><td>$value</td></tr>";
                        }
                    }
                }
                echo "</table>";
            } else {
                echo '<p class="error">✗ Archivo .env NO encontrado</p>';
                echo '<p class="info">Debes copiar .env.example a .env y configurarlo</p>';
            }
            ?>
        </div>

        <div class="section">
            <h2>5. Caché de Laravel</h2>
            <?php
            $cacheFiles = [
                'bootstrap/cache/routes-v7.php' => 'Route Cache',
                'bootstrap/cache/config.php' => 'Config Cache',
                'bootstrap/cache/services.php' => 'Services Cache',
            ];

            echo "<table>";
            foreach ($cacheFiles as $path => $name) {
                $fullPath = $projectRoot . '/' . $path;
                if (file_exists($fullPath)) {
                    $mtime = date('Y-m-d H:i:s', filemtime($fullPath));
                    echo "<tr><td>$name</td><td><span class='info'>✓ Existe (modificado: $mtime)</span></td></tr>";
                } else {
                    echo "<tr><td>$name</td><td><span class='success'>✓ No existe (sin caché)</span></td></tr>";
                }
            }
            echo "</table>";

            echo "<p class='info'>💡 Si hay problemas de rutas, ejecuta estos comandos vía SSH:</p>";
            echo "<pre>cd domains/dataflow.guaraniappstore.com/public_html\nphp artisan route:clear\nphp artisan config:clear\nphp artisan view:clear\nphp artisan cache:clear</pre>";
            ?>
        </div>

        <div class="section">
            <h2>6. Test de Rutas</h2>
            <?php
            echo "<p>Prueba estos enlaces:</p>";
            $baseUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'dataflow.guaraniappstore.com');
            $routes = [
                '/' => 'Home',
                '/login' => 'Login',
                '/register' => 'Register',
                '/dashboard' => 'Dashboard (requiere auth)',
                '/pricing' => 'Pricing',
                '/blog' => 'Blog',
            ];

            echo "<table>";
            foreach ($routes as $route => $name) {
                $url = $baseUrl . $route;
                echo "<tr><td>$name</td><td><a href='$url' target='_blank'>$url</a></td></tr>";
            }
            echo "</table>";
            ?>
        </div>

        <div class="section">
            <h2>7. Test de Assets (Imágenes)</h2>
            <?php
            $publicPath = __DIR__;
            $testAssets = [
                'favicon.ico',
                'images/og-image.jpg',
            ];

            echo "<table>";
            foreach ($testAssets as $asset) {
                $fullPath = $publicPath . '/' . $asset;
                $url = $baseUrl . '/' . $asset;
                $exists = file_exists($fullPath);

                if ($exists) {
                    $size = filesize($fullPath);
                    $sizeKb = round($size / 1024, 2);
                    echo "<tr><td>$asset</td><td><span class='success'>✓ Existe ($sizeKb KB)</span><br><a href='$url' target='_blank'>$url</a></td></tr>";
                } else {
                    echo "<tr><td>$asset</td><td><span class='error'>✗ No encontrado</span><br><small>Esperado en: $fullPath</small></td></tr>";
                }
            }
            echo "</table>";
            ?>
        </div>

        <div class="section">
            <h2>8. Recomendaciones</h2>
            <ul>
                <li>Asegúrate de que el Document Root apunta a la carpeta <code>public</code> de Laravel</li>
                <li>Los permisos de <code>storage/</code> y <code>bootstrap/cache/</code> deben ser 775 o 777</li>
                <li>El archivo <code>.env</code> debe existir y tener APP_URL configurado correctamente</li>
                <li>Si las rutas no funcionan, limpia el caché de rutas con <code>php artisan route:clear</code></li>
                <li>Las imágenes deben estar en <code>public/images/</code> para que <code>asset('images/...')</code> funcione</li>
            </ul>
        </div>

        <div class="section" style="background: #fef3c7; border-color: #f59e0b;">
            <p><strong>⚠️ IMPORTANTE:</strong> Elimina este archivo (<code>public/diagnostic.php</code>) después de usarlo por seguridad.</p>
        </div>
    </div>
</body>
</html>
