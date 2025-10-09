# Configuración para usar XAMPP con tu proyecto Laravel

## 1. Configurar Virtual Host

### Editar archivo de hosts (como Administrador)
Archivo: `C:\Windows\System32\drivers\etc\hosts`
Agregar línea:
```
127.0.0.1    gestion-operativa.local
```

### Configurar Virtual Host en Apache
Archivo: `D:\xampp\apache\conf\extra\httpd-vhosts.conf`
Agregar al final:
```apache
<VirtualHost *:80>
    DocumentRoot "D:/gestion_operativa_natha/gestion_operativa/public"
    ServerName gestion-operativa.local
    ServerAlias www.gestion-operativa.local
    
    <Directory "D:/gestion_operativa_natha/gestion_operativa/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/gestion-operativa-error.log"
    CustomLog "logs/gestion-operativa-access.log" common
</VirtualHost>
```

## 2. Configurar permisos Laravel
```bash
# Desde el directorio del proyecto
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 3. Acceder al proyecto
URL: http://gestion-operativa.local

## 4. Verificar GD en XAMPP
URL: http://gestion-operativa.local/test-gd