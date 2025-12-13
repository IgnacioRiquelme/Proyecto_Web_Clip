#!/bin/bash

echo "🚀 Iniciando proyecto Laravel con Docker..."

# 1. Verificar que Docker esté corriendo
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker no está corriendo. Por favor inicia Docker primero."
    exit 1
fi

# 2. Levantar contenedores Docker
echo "📦 Levantando contenedores Docker..."
docker-compose up -d

# 3. Esperar a que MySQL esté listo
echo "⏳ Esperando a que MySQL esté listo..."
sleep 5

# 4. Corregir permisos de node_modules/.vite si es necesario
if [ -d "node_modules/.vite/deps" ]; then
    OWNER=$(stat -c '%U' node_modules/.vite/deps)
    if [ "$OWNER" == "root" ]; then
        echo "🔧 Corrigiendo permisos de node_modules/.vite..."
        sudo chown -R $USER:$USER node_modules/.vite
    fi
fi

# 5. Iniciar Vite en modo desarrollo
echo "⚡ Iniciando Vite dev server..."
echo ""
echo "✅ Proyecto listo!"
echo "🌐 Laravel: http://localhost:8000"
echo "🗄️  phpMyAdmin: http://localhost:8080"
echo ""
npm run dev
