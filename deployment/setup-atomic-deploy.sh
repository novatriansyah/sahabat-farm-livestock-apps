#!/bin/bash
# ============================================================
# One-time setup: prepare server for atomic deploy workflow
# Run this ONCE on your Hostinger server via SSH.
# ============================================================
# Usage:
#   1. SSH into your server
#   2. cd {{ DEPLOY_PATH }}  (e.g., /home/u12345678/domains/sahabatfarmindonesia.com/public_html)
#   3. bash setup-atomic-deploy.sh
# ============================================================

set -e

echo "=== Atomic Deploy Setup ==="
echo ""

# --- Configuration ---
# Change this if your deploy path is different
DEPLOY_DIR="$(pwd)"
SHARED_DIR="${DEPLOY_DIR}/shared"
RELEASES_DIR="${DEPLOY_DIR}/releases"

echo "Deploy directory: ${DEPLOY_DIR}"
echo ""

# --- Step 1: Create shared directory ---
echo "[1/5] Creating shared/ directory..."
mkdir -p "${SHARED_DIR}"
echo "  ✓ ${SHARED_DIR}"

# --- Step 2: Move .env into shared ---
echo "[2/5] Moving .env into shared/..."
if [ -f "${DEPLOY_DIR}/.env" ]; then
  cp "${DEPLOY_DIR}/.env" "${SHARED_DIR}/.env"
  echo "  ✓ .env copied to shared/"
  echo "  ⚠  Original .env still at ${DEPLOY_DIR}/.env — you can remove it after verifying"
else
  echo "  ⚠  No .env found at ${DEPLOY_DIR}/.env"
  echo "  → Create one later: cp .env.example shared/.env && edit shared/.env"
fi

# --- Step 3: Move storage into shared ---
echo "[3/5] Moving storage/ into shared/..."
if [ -d "${DEPLOY_DIR}/storage" ]; then
  # Copy all contents (app, framework, logs) to shared/storage
  cp -r "${DEPLOY_DIR}/storage" "${SHARED_DIR}/storage"
  echo "  ✓ storage/ copied to shared/"
  echo "  ⚠  Original storage/ still at ${DEPLOY_DIR}/storage — keep until first deploy succeeds"
else
  echo "  ⚠  No storage/ found at ${DEPLOY_DIR}/storage"
  echo "  → Create later: mkdir -p shared/storage && chmod -R 775 shared/storage"
fi

# --- Step 4: Create releases directory ---
echo "[4/5] Creating releases/ directory..."
mkdir -p "${RELEASES_DIR}"
echo "  ✓ ${RELEASES_DIR}"

# --- Step 5: Create current symlink (point to first deploy) ---
echo "[5/5] Creating current/ symlink..."
# We don't have a release yet, so create a placeholder
# The first CI deploy will create a real release and update this symlink
ln -sfn "${RELEASES_DIR}" "${DEPLOY_DIR}/current" 2>/dev/null || true
echo "  ✓ current → releases/ (will be updated on first deploy)"
echo ""

echo "=== Setup complete ==="
echo ""
echo "Directory structure:"
echo "${DEPLOY_DIR}/"
echo "├── shared/"
echo "│   ├── .env              ← your production environment"
echo "│   └── storage/          ← uploads, logs, cache, sessions"
echo "├── releases/             ← each deploy creates a timestamped folder"
echo "├── current → releases/   ← symlink (updated by CI)"
echo "├── (your existing files) ← will be replaced by first deploy"
echo ""
echo "⚠  IMPORTANT:"
echo "  1. Keep the original .env and storage/ until first deploy succeeds"
echo "  2. After first successful deploy, you can remove:"
echo "     - ${DEPLOY_DIR}/.env (now in shared/)"
echo "     - ${DEPLOY_DIR}/storage (now in shared/)"
echo "     - Any old files not in shared/ or releases/"
echo "  3. Verify permissions:"
echo "     chmod -R 775 ${SHARED_DIR}/storage"
echo "     chmod 600 ${SHARED_DIR}/.env"
echo ""
echo "Next: push to master → CI will create first release and swap the symlink."