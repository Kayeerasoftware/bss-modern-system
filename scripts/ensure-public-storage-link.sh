#!/usr/bin/env bash
set -euo pipefail

project_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
link_path="${project_root}/public/storage"
target_path="${project_root}/storage/app/public"

mkdir -p "${project_root}/public" "${project_root}/storage/app"

if [[ -L "${link_path}" ]]; then
  current_target="$(readlink "${link_path}")"
  if [[ "${current_target}" == "${target_path}" ]]; then
    exit 0
  fi

  rm -f "${link_path}"
elif [[ -e "${link_path}" ]]; then
  rm -rf "${link_path}"
fi

if [[ ! -e "${target_path}" ]]; then
  mkdir -p "${target_path}"
fi

ln -s "${target_path}" "${link_path}"
