#!/usr/bin/env bash
set -euo pipefail

# Prepare Aiven CA certificate for MySQL SSL when provided as env content.
# Use either:
# - AIVEN_CA_PEM (full PEM text)
# - MYSQL_ATTR_SSL_CA (existing file path)

if [[ -n "${AIVEN_CA_PEM:-}" ]]; then
  CERT_DIR="storage/certs"
  CERT_PATH="${CERT_DIR}/aiven-ca.pem"
  mkdir -p "${CERT_DIR}"
  printf '%s\n' "${AIVEN_CA_PEM}" > "${CERT_PATH}"
  export MYSQL_ATTR_SSL_CA="${CERT_PATH}"
fi

