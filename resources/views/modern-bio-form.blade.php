@extends('layouts.app')

@section('title', 'BSS Bio Data Form')

@section('content')
    <div class="container mx-auto px-4 py-8 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen pt-16"
         x-data="bioFormApp()" :class="{ 'dark': darkMode }">
        
        <header class="bg-gradient-to-r from-blue-900 to-blue-700 text-white relative overflow-hidden py-8 text-center shadow-lg mb-8">
            <div class="relative z-10 container mx-auto px-6">
                <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">BSS Bio Data Form</h1>
                <p class="text-lg opacity-90">Complete Member Information System</p>
            </div>
        </header>

        <!-- Form Section -->
        <div class="form-section rounded-2xl p-8 mb-8 shadow-2xl bg-white dark:bg-gray-800">
            <div class="section-header text-gray-900 dark:text-white">
                <i class="fas fa-user-edit text-blue-500"></i>
                <span>Member Bio Data Form</span>
            </div>
            
            <form @submit.prevent="submitForm()" class="space-y-8">
                <!-- Personal Details -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-user text-blue-500 mr-3"></i>Personal Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Full Name *</label>
                            <input x-model="formData.full_name" type="text" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">National ID Number *</label>
                            <input x-model="formData.nin_no" type="text" required class="form-input">
                        </div>
                    </div>
                </div>
    
                <!-- Address Information -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt text-green-500 mr-3"></i>Address Information
                    </h3>
                    
                    <!-- Present Address -->
                    <div class="mb-6">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Present Address</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="form-label">Village</label>
                                <input x-model="formData.present_village" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Ward/Parish</label>
                                <input x-model="formData.present_ward" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Sub-county</label>
                                <input x-model="formData.present_subcounty" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">County</label>
                                <input x-model="formData.present_county" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">District</label>
                                <input x-model="formData.present_district" type="text" class="form-input">
                            </div>
                        </div>
                    </div>
    
                    <!-- Permanent Address -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">Permanent Address</h4>
                            <button type="button" @click="copyPresentToPermanent()" class="btn-secondary text-sm">
                                <i class="fas fa-copy mr-1"></i>Copy from Present
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="form-label">Village</label>
                                <input x-model="formData.permanent_village" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Ward/Parish</label>
                                <input x-model="formData.permanent_ward" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Sub-county</label>
                                <input x-model="formData.permanent_subcounty" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">County</label>
                                <input x-model="formData.permanent_county" type="text" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">District</label>
                                <input x-model="formData.permanent_district" type="text" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Contact Information -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-phone text-purple-500 mr-3"></i>Contact Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Email Address</label>
                            <input x-model="formData.email" type="email" class="form-input" placeholder="example@email.com">
                        </div>
                        <div>
                            <label class="form-label">Primary Phone</label>
                            <input x-model="formData.telephone[0]" type="tel" class="form-input" placeholder="+256...">
                        </div>
                        <div>
                            <label class="form-label">Secondary Phone (Optional)</label>
                            <input x-model="formData.telephone[1]" type="tel" class="form-input" placeholder="+256...">
                        </div>
                    </div>
                </div>
    
                <!-- Personal Details Extended -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-calendar text-orange-500 mr-3"></i>Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="form-label">Date of Birth *</label>
                            <input x-model="formData.dob" type="date" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Nationality</label>
                            <select x-model="formData.nationality" class="form-input">
                                <option value="">Select Nationality</option>
                                <option value="Ugandan">Ugandan</option>
                                <option value="Kenyan">Kenyan</option>
                                <option value="Tanzanian">Tanzanian</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Marital Status *</label>
                            <select x-model="formData.marital_status" required class="form-input">
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Occupation</label>
                            <input x-model="formData.occupation" type="text" class="form-input" placeholder="Your profession">
                        </div>
                    </div>
                </div>
    
                <!-- Birth Place -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-map text-red-500 mr-3"></i>Place of Birth
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Village</label>
                            <input x-model="formData.birth_village" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Ward/Parish</label>
                            <input x-model="formData.birth_ward" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Sub-county</label>
                            <input x-model="formData.birth_subcounty" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">County</label>
                            <input x-model="formData.birth_county" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">District</label>
                            <input x-model="formData.birth_district" type="text" class="form-input">
                        </div>
                    </div>
                </div>
    
                <!-- Family Information -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg" x-show="formData.marital_status === 'Married'">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-heart text-pink-500 mr-3"></i>Spouse Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Spouse Full Name</label>
                            <input x-model="formData.spouse_name" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Spouse NIN Number</label>
                            <input x-model="formData.spouse_nin" type="text" class="form-input">
                        </div>
                    </div>
                </div>
    
                <!-- Parents & Next of Kin -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-users text-indigo-500 mr-3"></i>Family & Emergency Contact
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Father's Name</label>
                            <input x-model="formData.father_name" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Mother's Name</label>
                            <input x-model="formData.mother_name" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Next of Kin Name</label>
                            <input x-model="formData.next_of_kin" type="text" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Next of Kin NIN</label>
                            <input x-model="formData.next_of_kin_nin" type="text" class="form-input">
                        </div>
                    </div>
                </div>
    
                <!-- Children Information -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-child text-yellow-500 mr-3"></i>Children Information
                    </h3>
                    <div class="space-y-4">
                        <template x-for="(child, index) in formData.children" :key="index">
                            <div class="flex items-center space-x-4 p-4 bg-gray-200 dark:bg-gray-600 rounded-lg">
                                <input x-model="child.name" type="text" placeholder="Child's Name" class="form-input flex-1">
                                <input x-model="child.age" type="number" placeholder="Age" class="form-input w-20">
                                <button type="button" @click="removeChild(index)" class="btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="addChild()" class="btn-secondary">
                            <i class="fas fa-plus mr-2"></i>Add Child
                        </button>
                    </div>
                </div>
    
                <!-- Declaration -->
                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-pen-fancy text-teal-500 mr-3"></i>Declaration
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                I hereby declare that the information provided above is true and accurate to the best of my knowledge.
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Digital Signature *</label>
                                <input x-model="formData.signature" type="text" required class="form-input" placeholder="Type your full name as signature">
                            </div>
                            <div>
                                <label class="form-label">Declaration Date *</label>
                                <input x-model="formData.declaration_date" type="date" required class="form-input">
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-6">
                    <button type="button" @click="resetForm()" class="btn-secondary w-full sm:w-auto">
                        <i class="fas fa-undo mr-2"></i>Reset Form
                    </button>
                    <button type="submit" :disabled="isSubmitting" class="btn-primary w-full sm:w-auto relative">
                        <span x-show="!isSubmitting" class="flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Bio Data
                        </span>
                        <span x-show="isSubmitting" class="flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Success/Error Messages -->
        <div x-show="showMessage" x-transition class="fixed top-4 right-4 z-50">
            <div :class="messageType === 'success' ? 'bg-green-500' : 'bg-red-500'" 
                 class="text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
                <i :class="messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
                <span x-text="message"></span>
                <button @click="showMessage = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        .form-section { 
            background: rgba(255, 255, 255, 0.08); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        .form-section:hover {
            transform: translateY(-5px) scale(1.005);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }
        .form-input, .form-textarea, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }
        .form-input::placeholder, .form-textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #4facfe;
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.3);
            outline: none;
        }
        .form-label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        .section-header {
            font-family: 'Space Grotesk', sans-serif;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.875rem; /* 30px */
            line-height: 2.25rem; /* 36px */
            font-weight: 700; /* bold */
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .section-header i {
            margin-right: 0.75rem;
        }

        /* Button styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px; /* Full rounded */
            font-weight: 600;
            color: white;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            transition: left 0.3s ease;
            z-index: -1;
        }
        .btn-primary:hover::before {
            left: 0;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px; /* Full rounded */
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); /* Red gradient */
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-1px);
        }

        /* General adjustments */
        body {
            font-family: 'Inter', sans-serif;
        }
        .font-space {
            font-family: 'Space Grotesk', sans-serif;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bioFormApp', () => ({
                darkMode: localStorage.getItem('darkMode') === 'true',
                isSubmitting: false,
                showMessage: false,
                message: '',
                messageType: 'success',
                formData: {
                    full_name: '',
                    nin_no: '',
                    present_village: '',
                    present_ward: '',
                    present_subcounty: '',
                    present_county: '',
                    present_district: '',
                    permanent_village: '',
                    permanent_ward: '',
                    permanent_subcounty: '',
                    permanent_county: '',
                    permanent_district: '',
                    email: '',
                    telephone: ['', ''],
                    dob: '',
                    nationality: 'Ugandan',
                    marital_status: '',
                    occupation: '',
                    birth_village: '',
                    birth_ward: '',
                    birth_subcounty: '',
                    birth_county: '',
                    birth_district: '',
                    spouse_name: '',
                    spouse_nin: '',
                    father_name: '',
                    mother_name: '',
                    next_of_kin: '',
                    next_of_kin_nin: '',
                    children: [],
                    signature: '',
                    declaration_date: new Date().toISOString().split('T')[0]
                },

                init() {
                    // Auto-save form data to localStorage
                    this.$watch('formData', () => {
                        localStorage.setItem('bioFormData', JSON.stringify(this.formData));
                    }, { deep: true });

                    // Load saved form data
                    const saved = localStorage.getItem('bioFormData');
                    if (saved) {
                        this.formData = { ...this.formData, ...JSON.parse(saved) };
                    }
                },

                copyPresentToPermanent() {
                    this.formData.permanent_village = this.formData.present_village;
                    this.formData.permanent_ward = this.formData.present_ward;
                    this.formData.permanent_subcounty = this.formData.present_subcounty;
                    this.formData.permanent_county = this.formData.present_county;
                    this.formData.permanent_district = this.formData.present_district;
                    this.showNotification('Address copied successfully!', 'success');
                },

                addChild() {
                    this.formData.children.push({ name: '', age: '' });
                },

                removeChild(index) {
                    this.formData.children.splice(index, 1);
                },

                async submitForm() {
                    if (this.isSubmitting) return;
                    
                    this.isSubmitting = true;
                    
                    try {
                        const response = await fetch('/api/bio-data', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const result = await response.json();

                        if (response.ok) {
                            this.showNotification('Bio data submitted successfully!', 'success');
                            localStorage.removeItem('bioFormData');
                            setTimeout(() => {
                                window.location.href = '/';
                            }, 2000);
                        } else {
                            throw new Error(result.message || 'Submission failed');
                        }
                    } catch (error) {
                        this.showNotification(error.message || 'An error occurred', 'error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                resetForm() {
                    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                        this.formData = {
                            full_name: '',
                            nin_no: '',
                            present_village: '',
                            present_ward: '',
                            present_subcounty: '',
                            present_county: '',
                            present_district: '',
                            permanent_village: '',
                            permanent_ward: '',
                            permanent_subcounty: '',
                            permanent_county: '',
                            permanent_district: '',
                            email: '',
                            telephone: ['', ''],
                            dob: '',
                            nationality: 'Ugandan',
                            marital_status: '',
                            occupation: '',
                            birth_village: '',
                            birth_ward: '',
                            birth_subcounty: '',
                            birth_county: '',
                            birth_district: '',
                            spouse_name: '',
                            spouse_nin: '',
                            father_name: '',
                            mother_name: '',
                            next_of_kin: '',
                            next_of_kin_nin: '',
                            children: [],
                            signature: '',
                            declaration_date: new Date().toISOString().split('T')[0]
                        };
                        localStorage.removeItem('bioFormData');
                        this.showNotification('Form reset successfully!', 'success');
                    }
                },

                showNotification(msg, type = 'success') {
                    this.message = msg;
                    this.messageType = type;
                    this.showMessage = true;
                    setTimeout(() => {
                        this.showMessage = false;
                    }, 5000);
                },

                async logout() {
                    try {
                        await fetch('{{ route('logout') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        // Clear all storage
                        localStorage.clear();
                        sessionStorage.clear();
                        
                        // Force redirect
                        window.location.replace('/login');
                    } catch (error) {
                        // Force logout even if API fails
                        localStorage.clear();
                        sessionStorage.clear();
                        window.location.replace('/login');
                    }
                }
            }))
        })
    </script>
@endsection