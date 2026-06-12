// Upload Picture Functions
function uploadMemberPicture(memberId) {
    this.uploadingMemberId = memberId;
    this.showUploadPictureModal = true;
}

function handlePictureUpload(event) {
    this.uploadedPictureFile = event.target.files[0];
}

async function submitPictureUpload() {
    if (!this.uploadedPictureFile || !this.uploadingMemberId) return;

    const formData = new FormData();
    formData.append('profile_picture', this.uploadedPictureFile);
    formData.append('_method', 'PUT');

    try {
        const response = await fetch(`/api/members/${this.uploadingMemberId}/picture`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        if (response.ok) {
            await this.fetchMembers();
            this.showUploadPictureModal = false;
            this.uploadedPictureFile = null;
            this.uploadingMemberId = null;
        }
    } catch (error) {
        console.error('Upload failed:', error);
    }
}
