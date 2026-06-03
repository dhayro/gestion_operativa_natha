# 🚀 GUÍA COMPLETA: DEPLOYMENT GESTION OPERATIVA EN PRODUCCIÓN

**Stack:** Laravel 11 + MySQL + Nginx + DigitalOcean + Hostinger Domain

---

## 📋 TABLA DE CONTENIDOS
1. [Paso 1: Comprar Dominio en Hostinger](#paso-1-comprar-dominio-en-hostinger)
2. [Paso 2: Crear Droplet en DigitalOcean](#paso-2-crear-droplet-en-digitalocean)
3. [Paso 3: Configurar Servidor](#paso-3-configurar-servidor)
4. [Paso 4: Instalar Dependencias](#paso-4-instalar-dependencias)
5. [Paso 5: Deploy del Proyecto](#paso-5-deploy-del-proyecto)
6. [Paso 6: Configurar SSL/HTTPS](#paso-6-configurar-sslhttps)
7. [Paso 7: Configurar DNS](#paso-7-configurar-dns)
8. [Paso 8: Monitoreo y Mantenimiento](#paso-8-monitoreo-y-mantenimiento)

---

# PASO 1: COMPRAR DOMINIO EN HOSTINGER

## 1.1 Acceder a Hostinger
- Ir a: https://www.hostinger.es/ (o .com)
- Crear cuenta o iniciar sesión

## 1.2 Buscar y Comprar Dominio
```
✓ Hacer clic en "Buscar dominio"
✓ Escribir tu dominio deseado (ej: migestio.com)
✓ Seleccionar la extensión (.com, .es, .pe, etc.)
✓ Hacer clic en "Añadir al carrito"
✓ Seleccionar duración (1, 2 o 3 años)
✓ Proceder al pago
```

### Recomendaciones:
- **Duración:** Mínimo 1 año
- **Protección WHOIS:** Activar (privacidad)
- **Precio estimado:** $3-15 USD/año

## 1.3 Completar Compra
```
✓ Ingresar datos de facturación
✓ Seleccionar método de pago (Tarjeta, PayPal)
✓ Confirmar compra
```

## 1.4 Acceder al Panel de Control
```
✓ Ir a: https://hpanel.hostinger.com/
✓ Buscar tu dominio en "Mis dominios"
✓ Hacer clic en "Gestionar DNS" o "Nameservers"
```

## 1.5 Cambiar Nameservers (IMPORTANTE)
Necesitamos apuntar a DigitalOcean para manejar el DNS.

**Tener a mano los nameservers de DigitalOcean:**
- `ns1.digitalocean.com`
- `ns2.digitalocean.com`
- `ns3.digitalocean.com`

En Hostinger:
```
✓ Ir a "Gestionar DNS"
✓ Buscar "Nameservers" o "Servidores de nombres"
✓ Cambiar de los nameservers de Hostinger a:
  - ns1.digitalocean.com
  - ns2.digitalocean.com
  - ns3.digitalocean.com
✓ Guardar cambios
✓ Esperar 24-48 horas para propagación
```

⏱️ **Nota:** Los cambios de DNS pueden tardar hasta 48 horas

---

# PASO 2: CREAR DROPLET EN DIGITALOCEAN

## 2.1 Acceder a DigitalOcean
- Ir a: https://www.digitalocean.com/
- Crear cuenta o iniciar sesión
- Ingresar método de pago (tarjeta de crédito)

## 2.2 Crear Droplet
```
✓ Ir a "Products" → "Droplets"
✓ Hacer clic en "Create Droplet"
```

## 2.3 Configurar el Droplet

### Opción de Imagen
```
Seleccionar: Ubuntu 24.04 LTS x64
(Recomendado para Laravel)
```

### Plan
```
OPCIÓN RECOMENDADA: $6/mes
- CPU: 1 vCPU
- RAM: 1 GB
- Storage: 25 GB

Para proyecto pequeño/mediano está perfecto
Si expects mucho tráfico: $12/mes (2 vCPU, 2GB RAM)
```

### Ubicación del Centro de Datos
```
Seleccionar cercano a tus usuarios:
- Nueva York (si es América)
- Frankfurt (si es Europa)
- Singapur (si es Asia)

Para Perú: Recomiendo Nueva York o Toronto
```

### SSH Key (IMPORTANTE)
```
✓ Seleccionar "New SSH Key"
✓ Nombre: "mi-laptop" o tu nombre
✓ Generar en tu PC (Windows/Mac/Linux):

# En tu terminal local:
ssh-keygen -t rsa -b 4096 -f ~/.ssh/digitalocean_key

✓ Copiar contenido de: ~/.ssh/digitalocean_key.pub
✓ Pegar en DigitalOcean
✓ Hacer clic en "Add SSH Key"
```

### Hostname
```
mi-servidor-gestion-operativa
(o el nombre que prefieras)
```

### Proyectos
```
Crear nuevo proyecto o dejar default
```

## 2.4 Crear el Droplet
```
✓ Hacer clic en "Create Droplet"
✓ Esperar 1-2 minutos a que se cree
✓ Copiar la IP pública (ej: 123.45.67.89)
```

---

# PASO 3: CONFIGURAR SERVIDOR

## 3.1 Conectarse al Servidor

### En Windows (PowerShell)
```powershell
# Primero, copiar la SSH key privada a tu PC
# Guardar en: C:\Users\TuUsuario\.ssh\digitalocean_key

# Conectar:
ssh -i C:\Users\TuUsuario\.ssh\digitalocean_key root@TU_IP_PUBLICA
```

### En Mac/Linux
```bash
ssh -i ~/.ssh/digitalocean_key root@TU_IP_PUBLICA
```

## 3.2 Actualizar Sistema
```bash
apt update
apt upgrade -y
```

## 3.3 Crear Usuario No-Root (SEGURIDAD)
```bash
# Crear usuario
adduser deployuser
# Seguir las instrucciones (nombre, contraseña, etc.)

# Dar permisos sudo
usermod -aG sudo deployuser

# Copiar SSH key al nuevo usuario
su - deployuser
mkdir -p ~/.ssh
# Copiar la SSH key pública aquí
# O conectar después con: ssh -i key deployuser@IP
```

## 3.4 Configurar Firewall
```bash
# Habilitar UFW
ufw enable

# Permitir SSH
ufw allow 22

# Permitir HTTP
ufw allow 80

# Permitir HTTPS
ufw allow 443

# Ver estado
ufw status
```

---

# PASO 4: INSTALAR DEPENDENCIAS

## 4.1 Instalar PHP 8.2
```bash
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php
apt update
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip
```

## 4.2 Instalar MySQL
```bash
apt install -y mysql-server

# Asegurar MySQL
mysql_secure_installation
# Responder: Y a todas las preguntas
```

## 4.3 Instalar Nginx
```bash
apt install -y nginx

# Iniciar Nginx
systemctl start nginx
systemctl enable nginx
```

## 4.4 Instalar Node.js y npm
```bash
apt install -y nodejs npm

# Verificar
node --version
npm --version
```

## 4.5 Instalar Git
```bash
apt install -y git

# Configurar Git
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

## 4.6 Instalar Composer
```bash
cd /tmp
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Verificar
composer --version
```

## 4.7 Instalar Certbot (SSL)
```bash
apt install -y certbot python3-certbot-nginx
```

---

# PASO 5: DEPLOY DEL PROYECTO

## 5.1 Preparar Directorio
```bash
# Crear directorio para la aplicación
mkdir -p /var/www/gestion-operativa
cd /var/www/gestion-operativa

# Cambiar permisos
chown -R deployuser:deployuser /var/www/gestion-operativa
```

## 5.2 Clonar Repositorio
```bash
# Como usuario deployuser
cd /var/www/gestion-operativa

# Clonar repositorio
git clone https://github.com/tu-usuario/gestion_operativa_natha.git .

# Entrar a la carpeta del proyecto
cd gestion_operativa
```

## 5.3 Instalar Dependencias PHP
```bash
composer install --optimize-autoloader --no-dev
```

## 5.4 Instalar Dependencias Frontend
```bash
npm install
npm run build
```

## 5.5 Crear Archivo .env para Producción
```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar .env
nano .env
```

### Contenido .env para Producción:
```env
APP_NAME="Gestion Operativa"
APP_ENV=production
APP_KEY=base64:VKF2xmArB0D97E55GvVMPbJtZwFEd5uX2pECvUFXucc=
APP_DEBUG=false
APP_URL=https://tudominio.com
APP_TIMEZONE=America/Lima

# BASE DE DATOS
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_operativa_prod
DB_USERNAME=gestion_user
DB_PASSWORD=CONTRASEÑA_FUERTE_AQUI

# SESIÓN Y CACHÉ
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# MAIL (Opcional, pero recomendado)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="Gestion Operativa"

# LOGGING
LOG_CHANNEL=stack
LOG_LEVEL=notice
```

**Guardar:** Ctrl+X → Y → Enter

## 5.6 Generar Key de la Aplicación
```bash
php artisan key:generate
```

## 5.7 Crear Base de Datos en MySQL
```bash
# Conectar a MySQL
mysql -u root -p

# Dentro de MySQL:
CREATE DATABASE gestion_operativa_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'gestion_user'@'localhost' IDENTIFIED BY 'CONTRASEÑA_FUERTE_AQUI';

GRANT ALL PRIVILEGES ON gestion_operativa_prod.* TO 'gestion_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

## 5.8 Ejecutar Migraciones
```bash
php artisan migrate --force
php artisan db:seed --force
```

## 5.9 Optimizar Laravel
```bash
php artisan optimize
php artisan view:cache
php artisan route:cache
php artisan config:cache
```

## 5.10 Establecer Permisos
```bash
# Desde /var/www/gestion-operativa/gestion_operativa
chmod -R 755 bootstrap/cache storage
chmod -R 755 bootstrap/cache
chmod -R 755 storage
chown -R www-data:www-data bootstrap/cache storage
```

---

# PASO 6: CONFIGURAR SSL/HTTPS

## 6.1 Configurar Nginx
```bash
# Editar configuración de Nginx
sudo nano /etc/nginx/sites-available/default
```

### Reemplazar contenido con:
```nginx
server {
    listen 80;
    listen [::]:80;
    
    server_name tudominio.com www.tudominio.com;
    
    root /var/www/gestion-operativa/gestion_operativa/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
    
    client_max_body_size 100M;
}
```

## 6.2 Validar Configuración de Nginx
```bash
nginx -t
```

## 6.3 Recargar Nginx
```bash
systemctl reload nginx
```

## 6.4 Obtener Certificado SSL
```bash
# Antes de hacer esto, asegúrate que tu dominio esté apuntando al servidor
# Espera a que DNS se propague (24-48 horas)

sudo certbot --nginx -d tudominio.com -d www.tudominio.com
```

### Responder preguntas:
```
1. Ingresar email
2. Aceptar términos (y)
3. Compartir email (y/n) - tu preferencia
4. Redirigir HTTP a HTTPS (2)
```

## 6.5 Renovación Automática de SSL
```bash
# Probar renovación
sudo certbot renew --dry-run

# Habilitar renovación automática (ya viene habilitada)
sudo systemctl enable certbot.timer
```

---

# PASO 7: CONFIGURAR DNS

## 7.1 Esperar Propagación de DNS
```
Después de cambiar nameservers a DigitalOcean en Hostinger,
esperar 24-48 horas para propagación completa.

Verificar con: https://www.whatsmydns.net/
Ingresar tu dominio
```

## 7.2 Agregar Dominio a DigitalOcean
```
✓ En DigitalOcean, ir a "Networking" → "Domains"
✓ Hacer clic en "Add a Domain"
✓ Escribir tu dominio (ej: tudominio.com)
✓ Seleccionar el Droplet
✓ Hacer clic en "Add Domain"
```

## 7.3 Agregar Registros DNS (Si es necesario)
```
DigitalOcean debería crear automáticamente:
- A record: apuntando a tu IP del Droplet
- WWW record: apuntando al dominio

Verificar en "Networking" → "Domains" → Tu dominio
```

## 7.4 Verificar DNS
```bash
# Desde tu PC
nslookup tudominio.com

# Debería retornar la IP de tu Droplet
```

---

# PASO 8: MONITOREO Y MANTENIMIENTO

## 8.1 Backup Automático de Base de Datos
```bash
# Crear script de backup
sudo nano /var/backups/backup_db.sh
```

### Contenido del script:
```bash
#!/bin/bash

BACKUP_DIR="/var/backups/mysql"
DB_USER="gestion_user"
DB_PASSWORD="CONTRASEÑA_AQUI"
DB_NAME="gestion_operativa_prod"
RETENTION_DAYS=7

mkdir -p $BACKUP_DIR

FILENAME="$BACKUP_DIR/gestion_operativa_$(date +%Y%m%d_%H%M%S).sql"

mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME > $FILENAME

# Comprimir
gzip $FILENAME

# Eliminar backups antiguos
find $BACKUP_DIR -type f -mtime +$RETENTION_DAYS -delete

echo "Backup realizado: $FILENAME.gz"
```

### Hacer ejecutable y agregar a cron:
```bash
chmod +x /var/backups/backup_db.sh

# Editar crontab
sudo crontab -e

# Agregar línea (ejecutar diariamente a las 2 AM):
0 2 * * * /var/backups/backup_db.sh
```

## 8.2 Monitoreo de Logs
```bash
# Ver logs de la aplicación
tail -f /var/www/gestion-operativa/gestion_operativa/storage/logs/laravel.log

# Ver logs de Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Ver logs de PHP-FPM
tail -f /var/log/php8.2-fpm.log
```

## 8.3 Actualizar Aplicación
```bash
cd /var/www/gestion-operativa/gestion_operativa

# Pull de cambios
git pull origin main

# Instalar nuevas dependencias
composer install --optimize-autoloader --no-dev

# Compilar assets
npm run build

# Ejecutar migraciones (si las hay)
php artisan migrate --force

# Limpiar caché
php artisan optimize:clear
php artisan optimize
```

## 8.4 Monitorear Recursos del Servidor
```bash
# Ver CPU, RAM, Disco
free -h
df -h
top

# Instalar htop (más visual)
sudo apt install htop
htop
```

## 8.5 Establecer Cron Job para Mantenimiento de Laravel
```bash
# Editar crontab
sudo crontab -e

# Agregar:
* * * * * cd /var/www/gestion-operativa/gestion_operativa && php artisan schedule:run >> /dev/null 2>&1
```

---

# 🔍 CHECKLIST FINAL PRE-LANZAMIENTO

```
DOMINIO:
✓ Dominio comprado en Hostinger
✓ Nameservers cambiados a DigitalOcean
✓ DNS propagado (verificar con whatsmydns.net)

SERVIDOR:
✓ Droplet creado en DigitalOcean
✓ SSH key configurada
✓ Firewall habilitado
✓ Usuario no-root creado

DEPENDENCIAS:
✓ PHP 8.2 instalado
✓ MySQL instalado
✓ Nginx instalado
✓ Node.js instalado
✓ Git instalado
✓ Composer instalado

APLICACIÓN:
✓ Repositorio clonado
✓ Dependencias PHP instaladas (composer)
✓ Dependencias JS instaladas (npm)
✓ Assets compilados (npm run build)
✓ .env configurado para producción
✓ APP_KEY generado
✓ Base de datos creada en MySQL
✓ Migraciones ejecutadas
✓ Permisos establecidos correctamente

SEGURIDAD:
✓ SSL/HTTPS configurado con Certbot
✓ Nginx configurado correctamente
✓ PHP-FPM configurado
✓ Firewall con reglas correctas
✓ APP_DEBUG=false

MANTENIMIENTO:
✓ Backup de BD automatizado
✓ Logs configurados
✓ Monitoreo implementado
```

---

# 📞 TROUBLESHOOTING COMÚN

## Error: "Connection refused" en MySQL
```bash
# Reiniciar MySQL
sudo systemctl restart mysql

# Verificar estado
sudo systemctl status mysql
```

## Error 502 Bad Gateway en Nginx
```bash
# Ver logs
tail -f /var/log/nginx/error.log

# Verificar PHP-FPM
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm

# Verificar sintaxis Nginx
nginx -t
```

## Permiso denegado en storage/
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## DNS no funciona después de 48h
```bash
# Limpiar caché local
# Windows: ipconfig /flushdns
# Mac: sudo dscacheutil -flushcache
# Linux: sudo systemctl restart systemd-resolved
```

## Certificado SSL no se genera
```bash
# Asegúrate que el dominio esté en DNS primero
# Esperar a propagación completa
# Verificar puertos 80 y 443 abiertos

# Intentar con debug
sudo certbot certonly --nginx -d tudominio.com -v
```

---

# 📚 RECURSOS ÚTILES

- **DigitalOcean Docs:** https://docs.digitalocean.com
- **Laravel Deployment:** https://laravel.com/docs/11/deployment
- **Nginx Documentation:** https://nginx.org/en/docs/
- **MySQL Documentation:** https://dev.mysql.com/doc/
- **Let's Encrypt/Certbot:** https://certbot.eff.org/

---

**Tiempo estimado total: 2-4 horas**
**Costo mensual: ~$9-15 USD (dominio + droplet)**

¡Éxito con tu deployment! 🚀
