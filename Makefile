# =============================================================================
# Makefile — frontend ventas
# Stack: React 19 · TypeScript 5.7 · Vite 7 · TailwindCSS 4
# =============================================================================

.DEFAULT_GOAL := help
.PHONY: help dev build preview install clean lint lint-fix format \
        type-check ts-check outdated analyze docker-build docker-run \
        docker-stop docker-clean nuke

# -----------------------------------------------------------------------------
# Variables
# -----------------------------------------------------------------------------
PKG_MANAGER := npm
NODE_MODULES := node_modules
DIST        := dist

# Colores
BOLD  := \033[1m
RESET := \033[0m
GREEN := \033[0;32m
CYAN  := \033[0;36m
YELLOW:= \033[0;33m
RED   := \033[0;31m

# =============================================================================
# AYUDA
# =============================================================================

help: ## Muestra esta ayuda
	@echo ""
	@echo "$(BOLD)frontend-ventas$(RESET) — Comandos disponibles"
	@echo "────────────────────────────────────────────────────"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""

# =============================================================================
# DESARROLLO
# =============================================================================

dev: ## Inicia el servidor de desarrollo (Vite)
	@echo "$(GREEN)Iniciando servidor de desarrollo...$(RESET)"
	$(PKG_MANAGER) run start

install: ## Instala dependencias de Node
	@echo "$(GREEN)Instalando dependencias...$(RESET)"
	$(PKG_MANAGER) install

install-clean: ## Reinstala dependencias desde cero (borra node_modules)
	@echo "$(YELLOW)Limpiando node_modules...$(RESET)"
	rm -rf $(NODE_MODULES)
	$(PKG_MANAGER) install

# =============================================================================
# BUILD
# =============================================================================

build: ## Genera el build de producción en /dist
	@echo "$(GREEN)Construyendo para producción...$(RESET)"
	$(PKG_MANAGER) run build

build-dev: ## Build con variables de entorno de desarrollo
	@echo "$(GREEN)Construyendo con env desarrollo...$(RESET)"
	$(PKG_MANAGER) run build -- --mode development

preview: build ## Build + previsualización local del bundle
	@echo "$(GREEN)Previsualizando build...$(RESET)"
	$(PKG_MANAGER) run preview

# =============================================================================
# CALIDAD DE CÓDIGO
# =============================================================================

lint: ## Ejecuta ESLint (solo reporta)
	@echo "$(CYAN)Ejecutando ESLint...$(RESET)"
	$(PKG_MANAGER) run lint

lint-fix: ## Ejecuta ESLint y corrige automáticamente
	@echo "$(CYAN)Corrigiendo errores de ESLint...$(RESET)"
	$(PKG_MANAGER) run lint:fix

prettier: ## Verifica formato con Prettier (solo reporta)
	@echo "$(CYAN)Verificando formato Prettier...$(RESET)"
	$(PKG_MANAGER) run prettier

prettier-fix: ## Aplica formato Prettier
	@echo "$(CYAN)Aplicando formato Prettier...$(RESET)"
	$(PKG_MANAGER) run prettier:fix

format: ## Aplica Prettier + ESLint (full format)
	@echo "$(CYAN)Formateando código completo...$(RESET)"
	$(PKG_MANAGER) run format

type-check: ## Verifica tipos TypeScript sin emitir archivos
	@echo "$(CYAN)Verificando tipos TypeScript...$(RESET)"
	npx tsc --noEmit

check: lint type-check ## Lint + type-check (CI local)
	@echo "$(GREEN)Todas las verificaciones pasaron.$(RESET)"

# =============================================================================
# ANÁLISIS
# =============================================================================

analyze: build ## Analiza el tamaño del bundle (requiere rollup-plugin-visualizer)
	@echo "$(CYAN)Analizando bundle...$(RESET)"
	@echo "$(YELLOW)Tip: instala rollup-plugin-visualizer para visualizar el bundle$(RESET)"
	$(PKG_MANAGER) run build -- --mode analysis 2>/dev/null || $(PKG_MANAGER) run build

outdated: ## Lista dependencias desactualizadas
	@echo "$(CYAN)Dependencias desactualizadas:$(RESET)"
	$(PKG_MANAGER) outdated

deps: ## Lista todas las dependencias instaladas
	$(PKG_MANAGER) list --depth=0

# =============================================================================
# LIMPIEZA
# =============================================================================

clean: ## Elimina el directorio dist/
	@echo "$(YELLOW)Limpiando dist/...$(RESET)"
	rm -rf $(DIST)

clean-cache: ## Limpia caché de Vite
	@echo "$(YELLOW)Limpiando caché de Vite...$(RESET)"
	rm -rf node_modules/.vite

nuke: ## Limpia dist/ + node_modules/ (reinstalar después con make install)
	@echo "$(RED)Eliminando dist/ y node_modules/...$(RESET)"
	rm -rf $(DIST) $(NODE_MODULES)

# =============================================================================
# DOCKER
# =============================================================================

docker-build: ## Construye la imagen Docker del frontend
	@echo "$(GREEN)Construyendo imagen Docker...$(RESET)"
	docker build -t cenefco-admin:latest .

docker-run: ## Ejecuta el contenedor del frontend en puerto 80
	@echo "$(GREEN)Iniciando contenedor frontend en :80...$(RESET)"
	docker run -d --name cenefco_admin -p 80:80 cenefco-admin:latest

docker-stop: ## Detiene el contenedor del frontend
	@echo "$(YELLOW)Deteniendo contenedor frontend...$(RESET)"
	docker stop cenefco_admin && docker rm cenefco_admin

docker-logs: ## Ver logs del contenedor
	docker logs -f cenefco_admin

docker-clean: ## Elimina imagen y contenedor del frontend
	@echo "$(RED)Eliminando imagen cenefco-admin...$(RESET)"
	docker rmi cenefco-admin:latest 2>/dev/null || true
