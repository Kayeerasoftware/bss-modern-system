<div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-show="activeLink === 'projects'" id="projects">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Projects Management</h2>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Projects</h3>
        <button @click="showAddProjectModal = true" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            <i class="fas fa-plus mr-2"></i>Add Project
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <template x-for="project in projects" :key="project.id">
            <div class="border rounded-lg p-4">
                <h4 class="font-semibold" x-text="project.name"></h4>
                <p class="text-sm text-gray-600" x-text="project.description"></p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
