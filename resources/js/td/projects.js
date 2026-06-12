// TD projects management
export function initTDProjects() {
    setupProjectFilters();
    loadProjectTimeline();
}

function setupProjectFilters() {
    // Setup project filtering
}

function loadProjectTimeline() {
    // Load project timeline view
}

export function updateProjectProgress(projectId, progress) {
    return fetch(`/api/td/projects/${projectId}/progress`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ progress: progress })
    });
}
