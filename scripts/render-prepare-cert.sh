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
  export MYSQL_ATTR_SSL_CA="$(pwd)/${CERT_PATH}"
else
  if [[ -n "${MYSQL_ATTR_SSL_CA:-}" ]]; then
    if [[ -f "${MYSQL_ATTR_SSL_CA}" ]]; then
      export MYSQL_ATTR_SSL_CA="$(realpath "${MYSQL_ATTR_SSL_CA}")"
    else
      RESOLVED_SSL_CA=""
      for candidate in \
        "/etc/ssl/certs/${MYSQL_ATTR_SSL_CA}" \
        "/etc/pki/tls/certs/${MYSQL_ATTR_SSL_CA}" \
        "/etc/ssl/certs/ca-certificates.crt" \
        "/etc/pki/tls/certs/ca-bundle.crt"
      do
        if [[ -f "${candidate}" ]]; then
          RESOLVED_SSL_CA="${candidate}"
          break
        fi
      done

      if [[ -n "${RESOLVED_SSL_CA}" ]]; then
        export MYSQL_ATTR_SSL_CA="${RESOLVED_SSL_CA}"
      else
        unset MYSQL_ATTR_SSL_CA
      fi
    fi
  fi

  if [[ -z "${MYSQL_ATTR_SSL_CA:-}" && -f "/etc/ssl/certs/ca-certificates.crt" ]]; then
    # Fallback for Render/Debian images with system CA bundle preinstalled.
    export MYSQL_ATTR_SSL_CA="/etc/ssl/certs/ca-certificates.crt"
  fi
fi
