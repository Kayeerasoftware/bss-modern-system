// File upload functionality
export function initFileUpload(inputId) {
    const input = document.getElementById(inputId);
    // Setup file upload preview and validation
}

export function validateFileSize(file, maxSize = 5242880) {
    return file.size <= maxSize;
}

export function validateFileType(file, allowedTypes = []) {
    return allowedTypes.includes(file.type);
}
