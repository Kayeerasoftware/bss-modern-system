/**
 * Member Picture Management Component
 * Handles upload, preview, delete, and bulk operations for member profile pictures
 */
class MemberPictureManager {
    constructor(options = {}) {
        this.options = {
            uploadUrl: '/admin/members/{id}/picture/upload',
            deleteUrl: '/admin/members/{id}/picture',
            bulkUploadUrl: '/admin/members/pictures/bulk-upload',
            maxFileSize: 5 * 1024 * 1024,
            allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
            previewSelector: '#preview',
            placeholderSelector: '#placeholder',
            uploadButtonSelector: '#upload-btn',
            deleteButtonSelector: '#delete-btn',
            progressSelector: '#upload-progress',
            ...options
        };

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupDragAndDrop();
    }

    setupEventListeners() {
        document.addEventListener('change', (e) => {
            if (e.target.type === 'file' && e.target.accept.includes('image')) {
                this.handleFileSelect(e);
            }
        });

        document.addEventListener('click', (e) => {
            const uploadButton = e.target.closest(this.options.uploadButtonSelector);
            if (uploadButton) {
                this.triggerFileSelect(uploadButton);
            }
        });

        document.addEventListener('click', (e) => {
            const deleteButton = e.target.closest(this.options.deleteButtonSelector);
            if (deleteButton) {
                this.deletePicture(deleteButton);
            }
        });
    }

    setupDragAndDrop() {
        const dropZones = document.querySelectorAll('.picture-drop-zone');
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', this.handleDragOver.bind(this));
            zone.addEventListener('dragleave', this.handleDragLeave.bind(this));
            zone.addEventListener('drop', this.handleDrop.bind(this));
        });
    }

    handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    handleDragLeave(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');
    }

    handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        const files = Array.from(e.dataTransfer.files);
        const imageFiles = files.filter(file => this.options.allowedTypes.includes(file.type));

        if (imageFiles.length > 0) {
            this.processFiles(imageFiles, e.currentTarget);
        } else {
            this.showError('Please drop valid image files only.');
        }
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        this.processFiles(files, e.target.closest('.picture-container'));
    }

    processFiles(files, container) {
        if (!container) {
            return;
        }

        files.forEach(file => {
            if (this.validateFile(file)) {
                this.previewImage(file, container);
                this.uploadImage(file, container);
            }
        });
    }

    validateFile(file) {
        if (!this.options.allowedTypes.includes(file.type)) {
            this.showError(`Invalid file type: ${file.type}. Allowed types: ${this.options.allowedTypes.join(', ')}`);
            return false;
        }

        if (file.size > this.options.maxFileSize) {
            this.showError(`File too large: ${(file.size / 1024 / 1024).toFixed(2)}MB. Maximum size: ${(this.options.maxFileSize / 1024 / 1024)}MB`);
            return false;
        }

        return true;
    }

    previewImage(file, container) {
        const reader = new FileReader();
        const preview = container.querySelector(this.options.previewSelector);
        const placeholder = container.querySelector(this.options.placeholderSelector);

        reader.onload = (e) => {
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        };

        reader.readAsDataURL(file);
    }

    async uploadImage(file, container) {
        const memberId = container.dataset.memberId;
        if (!memberId) {
            // Create member page has no member id yet; persist on form submit.
            return;
        }

        const formData = new FormData();
        formData.append('profile_picture', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        const progressBar = container.querySelector(this.options.progressSelector);
        const uploadUrl = this.options.uploadUrl.replace('{id}', memberId);

        try {
            this.showProgress(progressBar, 0);

            const response = await fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess('Picture uploaded successfully!');
                this.updatePictureInfo(container, result.picture_info);
            } else {
                this.showError(result.message || 'Upload failed');
            }
        } catch (error) {
            this.showError('Upload failed: ' + error.message);
        } finally {
            this.hideProgress(progressBar);
        }
    }

    async deletePicture(button) {
        const container = button.closest('.picture-container');
        if (!container) {
            return;
        }

        const memberId = container.dataset.memberId;
        if (!memberId) {
            this.showError('Member ID not found');
            return;
        }

        if (!confirm('Are you sure you want to delete this picture?')) {
            return;
        }

        const deleteUrl = this.options.deleteUrl.replace('{id}', memberId);

        try {
            const response = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess('Picture deleted successfully!');
                this.resetPictureDisplay(container);
            } else {
                this.showError(result.message || 'Delete failed');
            }
        } catch (error) {
            this.showError('Delete failed: ' + error.message);
        }
    }

    async bulkUpload(files, memberIds) {
        const formData = new FormData();

        files.forEach((file, index) => {
            formData.append(`pictures[${index}]`, file);
            formData.append(`member_ids[${index}]`, memberIds[index]);
        });

        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const response = await fetch(this.options.bulkUploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess(`Bulk upload completed! ${result.results.length} pictures processed.`);
                return result.results;
            }

            this.showError(result.message || 'Bulk upload failed');
        } catch (error) {
            this.showError('Bulk upload failed: ' + error.message);
        }

        return null;
    }

    triggerFileSelect(button) {
        const container = button.closest('.picture-container');
        if (!container) {
            return;
        }

        const fileInput = container.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.click();
        }
    }

    updatePictureInfo(container, pictureInfo) {
        const preview = container.querySelector(this.options.previewSelector);
        const placeholder = container.querySelector(this.options.placeholderSelector);

        if (pictureInfo.exists && preview) {
            preview.src = pictureInfo.url;
            preview.classList.remove('hidden');
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        }
    }

    resetPictureDisplay(container) {
        const preview = container.querySelector(this.options.previewSelector);
        const placeholder = container.querySelector(this.options.placeholderSelector);
        const fileInput = container.querySelector('input[type="file"]');

        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        if (placeholder) {
            placeholder.classList.remove('hidden');
        }
        if (fileInput) {
            fileInput.value = '';
        }
    }

    showProgress(progressBar, percent) {
        if (progressBar) {
            progressBar.style.display = 'block';
            progressBar.style.width = percent + '%';
        }
    }

    hideProgress(progressBar) {
        if (progressBar) {
            progressBar.style.display = 'none';
        }
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle text-green-500' : type === 'error' ? 'exclamation-circle text-red-500' : 'info-circle text-blue-500'} mr-2"></i>
                <span class="text-sm font-medium">${message}</span>
                <button class="ml-auto text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        if (type === 'success') {
            notification.classList.add('bg-green-50', 'border', 'border-green-200', 'text-green-800');
        } else if (type === 'error') {
            notification.classList.add('bg-red-50', 'border', 'border-red-200', 'text-red-800');
        } else {
            notification.classList.add('bg-blue-50', 'border', 'border-blue-200', 'text-blue-800');
        }

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.memberPictureManager = new MemberPictureManager();
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = MemberPictureManager;
}
