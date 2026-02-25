// Optimized Bio Data Form JavaScript
class BioDataOptimizer {
    constructor() {
        this.locationCache = new Map();
        this.elementCache = new Map();
        this.debounceTimers = new Map();
        this.pendingRequests = new Map();
        this.searchController = null;
        this.childCount = 0;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupCascadingDropdowns();
        this.preloadRegions();
    }
    
    // Cache DOM elements for better performance
    getElement(id) {
        if (!this.elementCache.has(id)) {
            this.elementCache.set(id, document.getElementById(id));
        }
        return this.elementCache.get(id);
    }
    
    // Debounce function with cleanup
    debounce(func, delay, key) {
        if (this.debounceTimers.has(key)) {
            clearTimeout(this.debounceTimers.get(key));
        }
        this.debounceTimers.set(key, setTimeout(() => {
            func();
            this.debounceTimers.delete(key);
        }, delay));
    }
    
    // Optimized fetch with request deduplication
    async fetchLocationData(url, cacheKey) {
        if (this.locationCache.has(cacheKey)) {
            return this.locationCache.get(cacheKey);
        }
        
        if (this.pendingRequests.has(cacheKey)) {
            return this.pendingRequests.get(cacheKey);
        }
        
        const request = fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        }).then(data => {
            this.locationCache.set(cacheKey, data);
            this.pendingRequests.delete(cacheKey);
            return data;
        }).catch(error => {
            console.error('Location fetch error:', error);
            this.pendingRequests.delete(cacheKey);
            return [];
        });
        
        this.pendingRequests.set(cacheKey, request);
        return request;
    }
    
    // Preload commonly used data
    async preloadRegions() {
        try {
            await this.fetchLocationData('/api/locations/regions', 'regions');
        } catch (error) {
            console.warn('Failed to preload regions:', error);
        }
    }
    
    // Batch DOM updates using DocumentFragment
    updateDatalist(listElement, options) {
        if (!listElement) return;
        
        requestAnimationFrame(() => {
            const fragment = document.createDocumentFragment();
            options.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                fragment.appendChild(optionElement);
            });
            listElement.innerHTML = '';
            listElement.appendChild(fragment);
        });
    }
    
    // Clear subsequent fields with batched updates
    clearSubsequentFields(prefix, level) {
        const fields = ['district', 'county', 'subcounty', 'ward', 'village'];
        const lists = ['district_list', 'county_list', 'subcounty_list', 'ward_list', 'village_list'];
        
        requestAnimationFrame(() => {
            for (let i = level; i < fields.length; i++) {
                const input = this.getElement(`${prefix}_${fields[i]}`);
                const list = this.getElement(`${prefix}_${lists[i]}`);
                if (input) input.value = '';
                if (list) list.innerHTML = '';
            }
        });
    }
    
    // Setup cascading dropdowns with optimized event handling
    setupCascadingDropdowns() {
        ['present', 'permanent', 'birth'].forEach(prefix => {
            this.setupDropdownForPrefix(prefix);
        });
    }
    
    setupDropdownForPrefix(prefix) {
        const regionInput = this.getElement(`${prefix}_region`);
        const districtInput = this.getElement(`${prefix}_district`);
        const countyInput = this.getElement(`${prefix}_county`);
        const subcountyInput = this.getElement(`${prefix}_subcounty`);
        const wardInput = this.getElement(`${prefix}_ward`);
        
        if (!regionInput) return;
        
        // Region change handler
        regionInput.addEventListener('input', (e) => {
            const region = e.target.value.trim();
            this.clearSubsequentFields(prefix, 0);
            
            if (region) {
                this.debounce(async () => {
                    try {
                        const districts = await this.fetchLocationData(
                            `/api/locations/districts/${encodeURIComponent(region)}`, 
                            `districts_${region}`
                        );
                        this.updateDatalist(this.getElement(`${prefix}_district_list`), districts);
                    } catch (error) {
                        console.error('Failed to load districts:', error);
                    }
                }, 150, `${prefix}_region`);
            }
        });
        
        // District change handler
        if (districtInput) {
            districtInput.addEventListener('input', (e) => {
                const district = e.target.value.trim();
                this.clearSubsequentFields(prefix, 1);
                
                if (district) {
                    this.debounce(async () => {
                        const counties = await this.fetchLocationData(
                            `/api/locations/counties/${encodeURIComponent(district)}`, 
                            `counties_${district}`
                        );
                        this.updateDatalist(this.getElement(`${prefix}_county_list`), counties);
                    }, 150, `${prefix}_district`);
                }
            });
        }
        
        // County change handler
        if (countyInput) {
            countyInput.addEventListener('input', (e) => {
                const county = e.target.value.trim();
                const district = districtInput?.value.trim();
                this.clearSubsequentFields(prefix, 2);
                
                if (district && county) {
                    this.debounce(async () => {
                        const subcounties = await this.fetchLocationData(
                            `/api/locations/subcounties?district=${encodeURIComponent(district)}&county=${encodeURIComponent(county)}`, 
                            `subcounties_${district}_${county}`
                        );
                        this.updateDatalist(this.getElement(`${prefix}_subcounty_list`), subcounties);
                    }, 150, `${prefix}_county`);
                }
            });
        }
        
        // Subcounty change handler
        if (subcountyInput) {
            subcountyInput.addEventListener('input', (e) => {
                const subcounty = e.target.value.trim();
                this.clearSubsequentFields(prefix, 3);
                
                if (subcounty) {
                    this.debounce(async () => {
                        const parishes = await this.fetchLocationData(
                            `/api/locations/parishes?subcounty=${encodeURIComponent(subcounty)}`, 
                            `parishes_${subcounty}`
                        );
                        this.updateDatalist(this.getElement(`${prefix}_ward_list`), parishes);
                    }, 150, `${prefix}_subcounty`);
                }
            });
        }
        
        // Ward change handler
        if (wardInput) {
            wardInput.addEventListener('input', (e) => {
                const ward = e.target.value.trim();
                this.clearSubsequentFields(prefix, 4);
                
                if (ward) {
                    this.debounce(async () => {
                        const villages = await this.fetchLocationData(
                            `/api/locations/villages?parish=${encodeURIComponent(ward)}`, 
                            `villages_${ward}`
                        );
                        this.updateDatalist(this.getElement(`${prefix}_village_list`), villages);
                    }, 150, `${prefix}_ward`);
                }
            });
        }
    }
    
    // Setup other event listeners
    setupEventListeners() {
        // Member search with request cancellation
        const memberSearch = this.getElement('member_search');
        if (memberSearch) {
            memberSearch.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                
                if (query.length < 2) {
                    this.getElement('member_dropdown').style.display = 'none';
                    return;
                }
                
                this.debounce(() => this.searchMembers(query), 200, 'member_search');
            });
            
            memberSearch.addEventListener('blur', () => {
                setTimeout(() => {
                    const dropdown = this.getElement('member_dropdown');
                    if (dropdown) dropdown.style.display = 'none';
                }, 200);
            });
        }
        
        // Nationality toggle
        const nationalitySelect = this.getElement('nationality_select');
        if (nationalitySelect) {
            nationalitySelect.addEventListener('change', (e) => {
                const otherDiv = this.getElement('other_nationality_div');
                const otherInput = this.getElement('other_nationality');
                
                if (e.target.value === 'Other') {
                    otherDiv.style.display = 'block';
                    otherInput.required = true;
                } else {
                    otherDiv.style.display = 'none';
                    otherInput.required = false;
                    otherInput.value = '';
                }
            });
        }
        
        // Children section
        document.querySelectorAll('input[name="has_children"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const childrenDetails = this.getElement('children_details');
                if (e.target.value === 'yes') {
                    childrenDetails.style.display = 'block';
                    if (this.childCount === 0) this.addChild();
                } else {
                    childrenDetails.style.display = 'none';
                    this.getElement('children_list').innerHTML = '';
                    this.childCount = 0;
                }
            });
        });
        
        const addChildBtn = this.getElement('add_child_btn');
        if (addChildBtn) {
            addChildBtn.addEventListener('click', () => this.addChild());
        }
    }
    
    // Optimized member search with cancellation
    async searchMembers(query) {
        if (this.searchController) {
            this.searchController.abort();
        }
        
        this.searchController = new AbortController();
        
        try {
            const response = await fetch(`/admin/members/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                signal: this.searchController.signal
            });
            
            if (response.ok) {
                const data = await response.json();
                this.showMemberDropdown(data || []);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Member search error:', error);
            }
        } finally {
            this.searchController = null;
        }
    }
    
    showMemberDropdown(members) {
        const dropdown = this.getElement('member_dropdown');
        if (!dropdown) return;
        
        if (members.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-gray-500">No members found</div>';
            dropdown.style.display = 'block';
            return;
        }
        
        dropdown.innerHTML = members.map(member => 
            `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b" onclick="bioDataOptimizer.selectMember(${member.id}, '${member.full_name}', '${member.member_id}')">
                <div class="font-semibold">${member.full_name}</div>
                <div class="text-sm text-gray-600">ID: ${member.member_id} | ${member.email || 'No email'}</div>
            </div>`
        ).join('');
        
        dropdown.style.display = 'block';
    }
    
    selectMember(id, name, memberId) {
        const memberSearch = this.getElement('member_search');
        const selectedMemberId = this.getElement('selected_member_id');
        const memberDisplay = this.getElement('member_display');
        const memberInfo = this.getElement('member_info');
        const dropdown = this.getElement('member_dropdown');
        
        if (memberSearch) memberSearch.value = `${name} (${memberId})`;
        if (selectedMemberId) selectedMemberId.value = id;
        if (memberDisplay) memberDisplay.textContent = `${name} (ID: ${memberId})`;
        if (memberInfo) memberInfo.style.display = 'block';
        if (dropdown) dropdown.style.display = 'none';
        
        this.loadMemberData(id);
    }
    
    async loadMemberData(id) {
        try {
            const response = await fetch(`/admin/members/${id}/details`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            if (response.ok) {
                const member = await response.json();
                this.populateForm(member);
            }
        } catch (error) {
            console.error('Error loading member data:', error);
        }
    }
    
    // Batch form population for better performance
    populateForm(member) {
        const updates = [];
        
        // Collect all updates first
        if (member.full_name || member.name) {
            updates.push(() => {
                const el = document.querySelector('input[name="full_name"]');
                if (el) el.value = member.full_name || member.name;
            });
        }
        
        if (member.nin_no) {
            updates.push(() => {
                const el = document.querySelector('input[name="nin_no"]');
                if (el) el.value = member.nin_no;
            });
        }
        
        // Add more field updates...
        
        // Execute all updates in a single animation frame
        requestAnimationFrame(() => {
            updates.forEach(update => update());
        });
    }
    
    addChild() {
        this.childCount++;
        const childDiv = document.createElement('div');
        childDiv.className = 'border border-gray-300 rounded-lg p-4 mb-3';
        childDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold text-gray-700">Child ${this.childCount}</h4>
                <button type="button" onclick="bioDataOptimizer.removeChild(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="children[${this.childCount}][name]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="children[${this.childCount}][dob]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="children[${this.childCount}][gender]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
        `;
        
        this.getElement('children_list').appendChild(childDiv);
    }
    
    removeChild(btn) {
        btn.closest('.border').remove();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.bioDataOptimizer = new BioDataOptimizer();
});