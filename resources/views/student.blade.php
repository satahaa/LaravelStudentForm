<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-4xl grid md:grid-cols-2 gap-8" x-data="studentApp()">
        
        <!-- Registration Form -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-2xl font-semibold mb-6 text-gray-900">Student Registration</h2>
            
            <div x-show="message" x-cloak class="mb-4 p-4 text-sm text-green-700 bg-green-50 rounded-lg" x-text="message"></div>
            <div x-show="error" x-cloak class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-lg" x-text="error"></div>

            <form @submit.prevent="submitForm" class="space-y-5">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Full Name</label>
                    <input type="text" x-model="form.name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Student ID</label>
                    <input type="text" x-model="form.student_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email Address</label>
                    <input type="email" x-model="form.email" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
                    <input type="text" x-model="form.phone" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                    <input type="text" x-model="form.department" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent outline-none transition">
                </div>
                
                <button type="submit" class="w-full bg-black text-white font-medium py-2.5 rounded-lg hover:bg-gray-800 transition shadow-sm" :disabled="loading">
                    <span x-show="!loading">Register Student</span>
                    <span x-show="loading">Registering...</span>
                </button>
            </form>
        </div>

        <!-- Search Feature -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[500px]">
            <h2 class="text-2xl font-semibold mb-6 text-gray-900">Student Directory</h2>
            
            <div class="relative mb-6">
                <input type="text" x-model="searchQuery" @input="searchStudents" class="w-full px-4 py-2.5 pl-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent outline-none transition bg-gray-50" placeholder="Search by ID or Name...">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="flex-1 overflow-y-auto pr-2">
                <template x-if="searchResults.length === 0 && searchQuery !== ''">
                    <div class="text-gray-400 text-sm text-center mt-10">No students found.</div>
                </template>
                <template x-if="searchResults.length === 0 && searchQuery === ''">
                    <div class="text-gray-400 text-sm text-center mt-10">Start typing to search existing students.</div>
                </template>

                <div class="space-y-3">
                    <template x-for="student in searchResults" :key="student.id">
                        <div class="p-4 border border-gray-100 rounded-xl hover:shadow-md transition bg-gray-50/50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900" x-text="student.name"></h3>
                                    <p class="text-xs text-gray-500 mt-1" x-text="student.email"></p>
                                    <p class="text-xs text-gray-500" x-text="student.department"></p>
                                </div>
                                <span class="bg-black text-white text-[10px] px-2 py-1 rounded-full font-medium" x-text="'ID: ' + student.student_id"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <script>
        function studentApp() {
            return {
                form: {
                    name: '',
                    student_id: '',
                    email: '',
                    phone: '',
                    department: ''
                },
                message: '',
                error: '',
                loading: false,
                
                searchQuery: '',
                searchResults: [],
                searchTimeout: null,

                async submitForm() {
                    this.loading = true;
                    this.message = '';
                    this.error = '';

                    try {
                        const response = await fetch('/student', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.message = data.message;
                            this.form = { name: '', student_id: '', email: '', phone: '', department: '' };
                            if(this.searchQuery) this.searchStudents(); // refresh search if active
                        } else {
                            this.error = data.message || 'An error occurred';
                        }
                    } catch (err) {
                        this.error = 'Failed to connect to the server.';
                    } finally {
                        this.loading = false;
                        setTimeout(() => this.message = '', 5000);
                    }
                },

                searchStudents() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(async () => {
                        if (!this.searchQuery) {
                            this.searchResults = [];
                            return;
                        }
                        try {
                            const response = await fetch(`/student/search?q=${encodeURIComponent(this.searchQuery)}`);
                            if (response.ok) {
                                this.searchResults = await response.json();
                            }
                        } catch (err) {
                            console.error('Search failed', err);
                        }
                    }, 300); // debounce by 300ms
                }
            }
        }
    </script>
</body>
</html>