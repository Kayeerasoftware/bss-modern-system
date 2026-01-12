<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Director Dashboard - BSS Investment Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/nav.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-50" x-data="tdDashboard()">
    @include('navs.topnav')
    @include('navs.sidenav')

    <!-- Main Content -->
    <div class="main-content ml-0 lg:ml-36 mt-12 transition-all duration-300" :class="sidebarCollapsed ? 'lg:ml-16' : 'lg:ml-36'">
        <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Project Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Completed Projects</p>
                        <p class="text-2xl font-bold text-green-600" x-text="projectStats.completed">8</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+2 this month</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-blue-600" x-text="projectStats.inProgress">12</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-cogs text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-blue-600">
                        <span x-text="formatCurrency(projectStats.totalBudget)">UGX 45M</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Team Members</p>
                        <p class="text-2xl font-bold text-purple-600" x-text="teamStats.totalMembers">24</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-purple-600">
                        <span x-text="teamStats.activeMembers + ' active'">18 active</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Avg. ROI</p>
                        <p class="text-2xl font-bold text-orange-600">14.2%</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>Above target</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Project Progress Overview -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Project Progress Overview</h3>
                    <select class="text-sm border rounded px-3 py-1">
                        <option>All Projects</option>
                        <option>Active Only</option>
                        <option>Completed</option>
                    </select>
                </div>
                <div style="height: 250px;">
                    <canvas id="projectProgressChart"></canvas>
                </div>
            </div>

            <!-- Resource Allocation -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Resource Allocation</h3>
                    <span class="text-sm text-gray-500">Current quarter</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="resourceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Active Projects & Team Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Active Projects -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Active Projects</h3>
                    <button @click="showAddProject = true" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>New Project
                    </button>
                </div>
                <div class="space-y-4">
                    <template x-for="project in activeProjects.slice(0, 5)" :key="project.id">
                        <div class="p-4 border rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-medium" x-text="project.name">Community Water Project</h4>
                                    <p class="text-sm text-gray-600" x-text="project.description">Installing clean water systems</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full" 
                                      :class="project.status === 'on-track' ? 'bg-green-100 text-green-800' : 
                                             project.status === 'at-risk' ? 'bg-yellow-100 text-yellow-800' : 
                                             'bg-red-100 text-red-800'"
                                      x-text="project.status">On Track</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Progress</span>
                                <span class="text-sm font-medium" x-text="project.progress + '%'">65%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                <div class="bg-indigo-500 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span x-text="'Budget: ' + formatCurrency(project.budget)">Budget: UGX 5M</span>
                                <span x-text="'Due: ' + project.deadline">Due: Dec 2024</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Team Performance -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Team Performance</h3>
                    <button class="text-indigo-600 text-sm hover:underline">View All Teams</button>
                </div>
                <div class="space-y-4">
                    <template x-for="team in teamPerformance" :key="team.id">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-users text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium" x-text="team.name">Water Team</h4>
                                        <p class="text-sm text-gray-600" x-text="team.members + ' members'">5 members</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-indigo-600" x-text="team.efficiency + '%'">92%</div>
                                    <div class="text-xs text-gray-500">Efficiency</div>
                                </div>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Projects: <span class="font-medium" x-text="team.projects">3</span></span>
                                <span class="text-gray-600">Completed: <span class="font-medium text-green-600" x-text="team.completed">2</span></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Project Timeline & Risk Assessment -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Project Timeline -->
            <div class="bg-white p-6 rounded-xl shadow-lg lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Project Timeline</h3>
                    <select class="text-sm border rounded px-3 py-1">
                        <option>Next 3 months</option>
                        <option>Next 6 months</option>
                        <option>This year</option>
                    </select>
                </div>
                <div class="space-y-4">
                    <template x-for="milestone in upcomingMilestones" :key="milestone.id">
                        <div class="flex items-center p-3 border-l-4" :class="milestone.priority === 'high' ? 'border-red-500 bg-red-50' : 
                                                                              milestone.priority === 'medium' ? 'border-yellow-500 bg-yellow-50' : 
                                                                              'border-green-500 bg-green-50'">
                            <div class="flex-1">
                                <h4 class="font-medium" x-text="milestone.title">Water System Installation</h4>
                                <p class="text-sm text-gray-600" x-text="milestone.project">Community Water Project</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium" x-text="milestone.date">Jan 25</div>
                                <div class="text-xs text-gray-500" x-text="milestone.daysLeft + ' days left'">5 days left</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Risk Assessment -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Risk Assessment</h3>
                <div class="space-y-4">
                    <div class="p-3 bg-red-50 rounded-lg border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-red-800">High Risk</h4>
                                <p class="text-sm text-red-600">2 projects</p>
                            </div>
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-yellow-800">Medium Risk</h4>
                                <p class="text-sm text-yellow-600">5 projects</p>
                            </div>
                            <i class="fas fa-exclamation-circle text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-green-800">Low Risk</h4>
                                <p class="text-sm text-green-600">5 projects</p>
                            </div>
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h4 class="font-medium mb-3">Key Performance Indicators</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">On-time Delivery</span>
                            <span class="font-medium text-green-600">87%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Budget Adherence</span>
                            <span class="font-medium text-blue-600">94%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Quality Score</span>
                            <span class="font-medium text-purple-600">91%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Team Satisfaction</span>
                            <span class="font-medium text-orange-600">89%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Project Modal -->
    <div x-show="showAddProject" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Create New Project</h3>
            <form @submit.prevent="addProject()">
                <div class="space-y-4">
                    <input type="text" x-model="newProject.name" placeholder="Project Name" class="w-full p-3 border rounded" required>
                    <textarea x-model="newProject.description" placeholder="Project Description" class="w-full p-3 border rounded" rows="3" required></textarea>
                    <input type="number" x-model="newProject.budget" placeholder="Budget (UGX)" class="w-full p-3 border rounded" required>
                    <input type="date" x-model="newProject.deadline" class="w-full p-3 border rounded" required>
                    <select x-model="newProject.priority" class="w-full p-3 border rounded" required>
                        <option value="">Select Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="showAddProject = false" class="px-4 py-2 text-gray-600 border rounded hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Create Project</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeTDCharts, 500);
        });
        
        function initializeTDCharts() {
            const progressCtx = document.getElementById('projectProgressChart')?.getContext('2d');
            if (progressCtx) {
                new Chart(progressCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Water Project', 'Education', 'Healthcare', 'Infrastructure', 'Technology'],
                        datasets: [{
                            label: 'Progress %',
                            data: [65, 100, 15, 80, 45],
                            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, max: 100 } }
                    }
                });
            }

            const resourceCtx = document.getElementById('resourceChart')?.getContext('2d');
            if (resourceCtx) {
                new Chart(resourceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Human Resources', 'Equipment', 'Materials', 'Technology', 'Contingency'],
                        datasets: [{
                            data: [40, 25, 20, 10, 5],
                            backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        }
        function tdDashboard() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
                showProfileDropdown: false,
                showLogoModal: false,
                showShareholderModal: false,
                showCalendarModal: false,
                showChatModal: false,
                showMemberChatModal: false,
                showLoanRequestModal: false,
                showOpportunitiesModal: false,
                showProfileViewModal: false,
                activeLink: 'overview',
                sidebarSearch: '',
                profilePicture: null,
                showAddProject: false,
                newProject: {},
                projectStats: {
                    completed: 8,
                    inProgress: 12,
                    totalBudget: 45000000
                },
                teamStats: {
                    totalMembers: 24,
                    activeMembers: 18
                },
                activeProjects: [
                    {id: 1, name: 'Community Water Project', description: 'Installing clean water systems', progress: 65, budget: 5000000, deadline: 'Dec 2024', status: 'on-track'},
                    {id: 2, name: 'Education Support Program', description: 'Providing scholarships', progress: 100, budget: 3000000, deadline: 'Jun 2024', status: 'completed'},
                    {id: 3, name: 'Healthcare Initiative', description: 'Mobile health clinics', progress: 15, budget: 8000000, deadline: 'Mar 2025', status: 'at-risk'}
                ],
                teamPerformance: [
                    {id: 1, name: 'Water Team', members: 5, efficiency: 92, projects: 3, completed: 2},
                    {id: 2, name: 'Education Team', members: 4, efficiency: 88, projects: 2, completed: 2},
                    {id: 3, name: 'Health Team', members: 6, efficiency: 85, projects: 4, completed: 1}
                ],
                upcomingMilestones: [
                    {id: 1, title: 'Water System Installation', project: 'Community Water Project', date: 'Jan 25', daysLeft: 5, priority: 'high'},
                    {id: 2, title: 'Equipment Procurement', project: 'Healthcare Initiative', date: 'Feb 10', daysLeft: 21, priority: 'medium'},
                    {id: 3, title: 'Phase 2 Planning', project: 'Education Support', date: 'Feb 15', daysLeft: 26, priority: 'low'}
                ],
                
                init() {
                    this.loadTdData();
                },
                
                async loadTdData() {
                    try {
                        const response = await fetch('/api/td-data');
                        const data = await response.json();
                        
                        this.activeProjects = data.projects.map(project => ({
                            id: project.id,
                            name: project.name,
                            description: project.description,
                            progress: project.progress,
                            budget: project.budget,
                            deadline: project.timeline,
                            status: project.progress === 100 ? 'completed' : 
                                   project.progress > 0 ? 'on-track' : 'planning'
                        }));
                        
                        this.projectStats = data.project_stats;
                        this.teamPerformance = data.team_performance;
                        this.upcomingMilestones = data.upcoming_milestones;
                        
                        this.initCharts(data);
                    } catch (error) {
                        console.error('Error loading TD data:', error);
                        this.initCharts();
                    }
                },
                
                formatCurrency(amount) {
                    return 'UGX ' + (amount/1000000).toFixed(1) + 'M';
                },
                
                addProject() {
                    fetch('/api/projects', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(this.newProject)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.activeProjects.push({
                                id: data.project.id,
                                name: data.project.name,
                                description: data.project.description,
                                progress: data.project.progress,
                                budget: data.project.budget,
                                deadline: data.project.timeline,
                                status: 'planning'
                            });
                            this.showAddProject = false;
                            this.newProject = {};
                            alert('Project created successfully!');
                        } else {
                            alert('Error creating project');
                        }
                    })
                    .catch(error => {
                        console.error('Error creating project:', error);
                        alert('Error creating project');
                    });
                },
                
                initCharts(data = null) {
                    // Clear existing charts
                    if (window.progressChart) window.progressChart.destroy();
                    if (window.resourceChart) window.resourceChart.destroy();
                    
                    // Project Progress Chart
                    const progressCtx = document.getElementById('projectProgressChart').getContext('2d');
                    
                    let projectNames = ['Water Project', 'Education', 'Healthcare', 'Infrastructure', 'Technology'];
                    let progressData = [65, 100, 15, 80, 45];
                    
                    if (data && data.project_progress) {
                        projectNames = Object.keys(data.project_progress);
                        progressData = Object.values(data.project_progress);
                    }
                    
                    window.progressChart = new Chart(progressCtx, {
                        type: 'bar',
                        data: {
                            labels: projectNames,
                            datasets: [{
                                label: 'Progress %',
                                data: progressData,
                                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });

                    // Resource Allocation Chart
                    const resourceCtx = document.getElementById('resourceChart').getContext('2d');
                    
                    let resourceData = [40, 25, 20, 10, 5];
                    if (data && data.resource_allocation) {
                        resourceData = [
                            data.resource_allocation.human_resources || 0,
                            data.resource_allocation.equipment || 0,
                            data.resource_allocation.materials || 0,
                            data.resource_allocation.technology || 0,
                            data.resource_allocation.contingency || 0
                        ];
                    }
                    
                    window.resourceChart = new Chart(resourceCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Human Resources', 'Equipment', 'Materials', 'Technology', 'Contingency'],
                            datasets: [{
                                data: resourceData,
                                backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
</body>
</html>