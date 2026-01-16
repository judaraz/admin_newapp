<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashReward Admin Panel - Game Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .admin-page { display: none; }
        .admin-page.active { display: block; animation: fadeIn 0.5s; }
        .sidebar-link { transition: all 0.2s; border-left: 3px solid transparent; }
        .sidebar-link.active, .sidebar-link:hover { background-color: #4f46e5; color: white; border-left-color: #818cf8; }
        .stat-card { background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06); }
        .input-field { border: 1px solid #cbd5e1; padding: 0.5rem 0.75rem; border-radius: 0.375rem; width: 100%; }
        .btn { padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600; transition: all 0.2s; cursor: pointer; }
        .btn:active { transform: scale(0.95); }
        .btn-primary { background-color: #4f46e5; color: white; border: none; }
        .btn-primary:hover { background-color: #4338ca; }
        .btn-secondary { background-color: #64748b; color: white; border: none; }
        .btn-danger { background-color: #ef4444; color: white; border: none; }
        .btn-success { background-color: #10b981; color: white; border: none; }
        .btn-warning { background-color: #f59e0b; color: white; border: none; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        #toast-notification { transition: opacity 0.5s ease-in-out; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #f9fafb; font-weight: 600; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .status-active { background-color: #d1fae5; color: #065f46; }
        .status-blocked { background-color: #fee2e2; color: #991b1b; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-visible { background-color: #3b82f6; color: white; }
        .status-hidden { background-color: #6b7280; color: white; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal { background: white; border-radius: 0.5rem; max-width: 500px; margin: 2rem auto; padding: 1.5rem; max-height: 80vh; overflow-y: auto; }
        .point-input { width: 100px; text-align: center; }
        .view-points-btn { background-color: #3b82f6; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; }
        .game-card { background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); }
        .time-input { width: 120px; }
        .switch { position: relative; display: inline-block; width: 60px; height: 34px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
        .slider:before { position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; }
        input:checked + .slider { background-color: #10b981; }
        input:checked + .slider:before { transform: translateX(26px); }
        .slider.round { border-radius: 34px; }
        .slider.round:before { border-radius: 50%; }
        .website-item { display: flex; gap: 10px; margin-bottom: 10px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb; }
        .website-field { flex: 1; }
        .website-field input { width: 100%; }
        .feature-toggle { display: flex; align-items: center; justify-content: space-between; margin: 1rem 0; }
        .feature-label { font-weight: 600; color: #374151; }
        .game-limits-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1.5rem 0; }
        .limit-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; }
        .limit-title { font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        .limit-value { font-size: 1.25rem; font-weight: 700; color: #1e40af; }
        .game-action-buttons { display: flex; gap: 0.5rem; margin-top: 1rem; }
        .settings-section { margin-bottom: 2rem; }
        .settings-card { background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); }
        .settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1rem 0; }
    </style>
</head>
<body>
    <!-- Loader -->
    <div id="loader" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-indigo-500"></div>
    </div>
    
    <!-- Login Section -->
    <div id="auth-section" class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Panel Login</h2>
            <input id="login-email" type="email" placeholder="Email" class="input-field mb-4">
            <input id="login-password" type="password" placeholder="Password" class="input-field mb-4">
            <button id="login-btn" class="btn btn-primary w-full">Login</button>
            <p id="login-error" class="text-red-500 text-sm mt-2 text-center"></p>
        </div>
    </div>

    <!-- Main Admin Panel -->
    <div id="panel-section" class="hidden">
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <aside class="w-64 bg-gray-800 text-gray-200 flex-shrink-0">
                <div class="p-4 text-2xl font-bold text-white">CashReward</div>
                <nav class="mt-6">
                    <a href="#" data-page="dashboard" class="sidebar-link active flex items-center py-3 px-4">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>Dashboard
                    </a>
                    <a href="#" data-page="users" class="sidebar-link flex items-center py-3 px-4">
                        <i data-lucide="users" class="w-5 h-5 mr-3"></i>Users
                    </a>
                    <a href="#" data-page="withdrawals" class="sidebar-link flex items-center py-3 px-4">
                        <i data-lucide="banknote" class="w-5 h-5 mr-3"></i>Withdrawals
                    </a>
                    <a href="#" data-page="games" class="sidebar-link flex items-center py-3 px-4">
                        <i data-lucide="gamepad-2" class="w-5 h-5 mr-3"></i>Game Management
                    </a>
                    <a href="#" data-page="notifications" class="sidebar-link flex items-center py-3 px-4">
                        <i data-lucide="send" class="w-5 h-5 mr-3"></i>Notifications
                    </a>
                    <a href="#" data-page="settings" class="sidebar-link flex items-center py-3 px-4">
                        <i data-lucide="settings" class="w-5 h-5 mr-3"></i>App Settings
                    </a>
                </nav>
                <div class="absolute bottom-0 w-64 p-4">
                    <button id="logout-btn" class="w-full bg-red-600 text-white py-2 rounded-lg">Logout</button>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                <div id="dashboard" class="admin-page active"></div>
                <div id="users" class="admin-page"></div>
                <div id="withdrawals" class="admin-page"></div>
                <div id="games" class="admin-page"></div>
                <div id="notifications" class="admin-page"></div>
                <div id="settings" class="admin-page"></div>
            </main>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-notification" class="hidden fixed top-5 right-5 text-white px-6 py-3 rounded-lg shadow-lg opacity-0 z-50">
        <p id="toast-message"></p>
    </div>

    <!-- Alert Modal -->
    <div id="custom-alert" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center shadow-xl">
            <p id="alert-message" class="mb-4 text-gray-800"></p>
            <button id="alert-ok-btn" class="btn btn-primary px-6">OK</button>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="custom-confirm" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center shadow-xl">
            <p id="confirm-message" class="mb-6 text-gray-800"></p>
            <div class="flex justify-center space-x-4">
                <button id="confirm-cancel-btn" class="btn bg-gray-200 text-gray-800 px-6">Cancel</button>
                <button id="confirm-ok-btn" class="btn btn-danger px-6">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Quick Game Settings Modal (from reference code) -->
    <div id="quick-game-settings-modal" class="modal-overlay">
        <div class="modal" style="max-width: 450px;">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="quick-game-title">Quick Settings</h3>
                <button onclick="closeQuickSettingsModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Play Limit -->
                <div>
                    <label class="block font-semibold mb-2">Daily Play Limit</label>
                    <div class="flex items-center space-x-2">
                        <input type="number" id="quick-play-limit" class="input-field" min="0" max="1000" placeholder="0 = unlimited">
                        <span class="text-gray-600 text-sm">plays per day</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">After this limit, game will not be playable for the day</p>
                </div>
                
                <!-- Visibility Toggle -->
                <div class="feature-toggle">
                    <div>
                        <label class="feature-label">Game Visibility</label>
                        <p class="text-sm text-gray-500">Show/hide this game in mini-app</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="quick-game-visible">
                        <span class="slider round"></span>
                    </label>
                </div>
                
                <!-- Additional Quick Settings -->
                <div>
                    <label class="block font-semibold mb-2">Reward Per Play</label>
                    <input type="number" id="quick-game-reward" class="input-field" min="0" placeholder="Points">
                </div>
                
                <div>
                    <label class="block font-semibold mb-2">Game Order</label>
                    <input type="number" id="quick-game-order" class="input-field" min="1" max="10" placeholder="1-10">
                    <p class="text-sm text-gray-500 mt-1">Display order in mini-app (1 = first)</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="closeQuickSettingsModal()" class="btn bg-gray-200 text-gray-800 px-6">Cancel</button>
                <button onclick="saveQuickGameSettings()" class="btn btn-primary px-6">Save Settings</button>
            </div>
        </div>
    </div>

    <!-- Edit User Points Modal -->
    <div id="edit-points-modal" class="modal-overlay">
        <div class="modal">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit User Points</h3>
                <button onclick="closeEditPointsModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-gray-600">User: <span id="edit-user-name" class="font-semibold"></span></p>
                <p class="text-gray-600">Email: <span id="edit-user-email" class="font-semibold"></span></p>
                <p class="text-gray-600">Current Balance: <span id="edit-current-balance" class="font-semibold"></span></p>
            </div>
            <div class="mb-6">
                <label class="block font-semibold mb-2">New Balance</label>
                <input type="number" id="new-balance-input" class="input-field" placeholder="Enter new balance">
            </div>
            <div class="flex justify-end space-x-2">
                <button onclick="closeEditPointsModal()" class="btn bg-gray-200 text-gray-800">Cancel</button>
                <button onclick="saveUserPoints()" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- User Point History Modal -->
    <div id="user-point-history-modal" class="modal-overlay">
        <div class="modal" style="max-width: 800px;">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Point History - <span id="history-user-name"></span></h3>
                <button onclick="closePointHistoryModal()" class="text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-gray-600">User ID: <span id="history-user-id" class="font-semibold"></span></p>
                <p class="text-gray-600">Total Points Earned: <span id="total-points-earned" class="font-semibold text-green-600"></span></p>
            </div>
            <div class="overflow-y-auto max-h-96">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2">Date & Time</th>
                            <th class="px-4 py-2">Task</th>
                            <th class="px-4 py-2">Points</th>
                            <th class="px-4 py-2">Country</th>
                            <th class="px-4 py-2">Details</th>
                        </tr>
                    </thead>
                    <tbody id="user-point-history-body">
                        <!-- Point history will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="closePointHistoryModal()" class="btn bg-gray-200 text-gray-800">Close</button>
            </div>
        </div>
    </div>

    <script type="module">
        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyA3palpYZDFdNpBDIF564YsiOAN2dtbxzA",
            authDomain: "tic-tac-toe-87d49.firebaseapp.com",
            projectId: "tic-tac-toe-87d49",
            storageBucket: "tic-tac-toe-87d49.firebasestorage.app",
            messagingSenderId: "1051665363188",
            appId: "1:1051665363188:web:c6389dddb74491a14ceea1"
        };
        
        const ADMIN_UIDS = ["pPICivnJ7ZafIfDeuhilCyg6kO22"];

        // Firebase Imports
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
        import { getAuth, onAuthStateChanged, signInWithEmailAndPassword, signOut } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-auth.js";
        import { 
            getFirestore, 
            doc, 
            getDoc, 
            updateDoc, 
            collection, 
            getDocs, 
            writeBatch, 
            enableIndexedDbPersistence, 
            query, 
            where, 
            onSnapshot, 
            addDoc, 
            serverTimestamp, 
            setDoc,
            orderBy,
            limit,
            arrayUnion
        } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore.js";

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const db = getFirestore(app);

        // Enable persistence
        enableIndexedDbPersistence(db).catch((err) => {
            if (err.code == 'failed-precondition') {
                console.warn('Firestore persistence failed: multiple tabs open.');
            } else if (err.code == 'unimplemented') {
                console.warn('Firestore persistence not available in this browser.');
            }
        });

        // DOM Elements
        const loader = document.getElementById('loader');
        let currentEditingUserId = null;
        let currentViewingUserId = null;
        let currentEditingGameId = null;

        // Initialize on load
        window.onload = () => {
            lucide.createIcons();
            setupEventListeners();
            onAuthStateChanged(auth, handleAuthStateChange);
        };

        // Auth State Handler
        function handleAuthStateChange(user) {
            if (user && ADMIN_UIDS.includes(user.uid)) {
                document.getElementById('auth-section').style.display = 'none';
                document.getElementById('panel-section').style.display = 'block';
                navigateTo('dashboard');
                loadDashboardData();
                loadUsers();
                loadWithdrawals();
                loadSettings();
            } else {
                document.getElementById('auth-section').style.display = 'flex';
                document.getElementById('panel-section').style.display = 'none';
                if (user) {
                    signOut(auth);
                    showAlert("You are not authorized to access this panel.");
                }
            }
        }

        // Setup Event Listeners
        function setupEventListeners() {
            document.getElementById('login-btn').addEventListener('click', handleLogin);
            document.getElementById('logout-btn').addEventListener('click', () => signOut(auth));
            
            // Navigation
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    navigateTo(link.dataset.page);
                });
            });
            
            // Alert/Confirm buttons
            document.getElementById('alert-ok-btn').addEventListener('click', () => {
                document.getElementById('custom-alert').classList.add('hidden');
            });
            
            document.getElementById('confirm-cancel-btn').addEventListener('click', () => {
                document.getElementById('custom-confirm').classList.add('hidden');
            });
        }

        // Login Handler
        async function handleLogin() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const errorEl = document.getElementById('login-error');
            errorEl.textContent = '';
            loader.style.display = 'flex';
            
            try {
                const userCredential = await signInWithEmailAndPassword(auth, email, password);
                if (!ADMIN_UIDS.includes(userCredential.user.uid)) {
                    await signOut(auth);
                    showAlert("You are not authorized to access this panel.");
                }
            } catch (error) {
                errorEl.textContent = "Invalid email or password.";
                console.error("Login error:", error);
            } finally {
                loader.style.display = 'none';
            }
        }

        // Navigation
        function navigateTo(pageId) {
            document.querySelectorAll('.admin-page').forEach(p => p.classList.remove('active'));
            document.getElementById(pageId).classList.add('active');
            
            document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
            document.querySelector(`.sidebar-link[data-page="${pageId}"]`).classList.add('active');

            if (pageId === 'dashboard') loadDashboard();
            if (pageId === 'users') loadUsers();
            if (pageId === 'withdrawals') loadWithdrawals();
            if (pageId === 'games') loadGames();
            if (pageId === 'notifications') loadNotifications();
            if (pageId === 'settings') loadSettings();
        }

        // Dashboard Functions (keep existing dashboard functions...)
        async function loadDashboard() {
            const page = document.getElementById('dashboard');
            page.innerHTML = `
                <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
                <div id="stats-grid" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="stat-card p-6"><h3 class="text-gray-500 text-lg">Total Users</h3><p id="total-users" class="text-4xl font-bold mt-2">0</p></div>
                    <div class="stat-card p-6"><h3 class="text-gray-500 text-lg">Total Withdrawals</h3><p id="total-withdrawals" class="text-4xl font-bold mt-2">0</p></div>
                    <div class="stat-card p-6"><h3 class="text-gray-500 text-lg">Pending Withdrawals</h3><p id="pending-withdrawals" class="text-4xl font-bold text-orange-500 mt-2">0</p></div>
                    <div class="stat-card p-6"><h3 class="text-gray-500 text-lg">Today's Earnings</h3><p id="today-earnings" class="text-4xl font-bold text-green-600 mt-2">0</p></div>
                </div>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-2xl font-bold mb-4">Recent Withdrawals</h2>
                        <div id="recent-withdrawals" class="bg-white shadow rounded-lg p-4">
                            Loading...
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold mb-4">Recent Users</h2>
                        <div id="recent-users" class="bg-white shadow rounded-lg p-4">
                            Loading...
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="text-2xl font-bold mb-4">Game Statistics</h2>
                    <div id="game-stats" class="bg-white shadow rounded-lg p-4">
                        Loading...
                    </div>
                </div>
            `;
            
            // Load real-time data
            loadDashboardData();
        }

        function loadDashboardData() {
            // Total Users (real-time)
            onSnapshot(collection(db, "users"), snap => {
                document.getElementById('total-users').textContent = snap.size;
            });

            // Total Withdrawals (real-time)
            onSnapshot(collection(db, "withdrawals"), snap => {
                document.getElementById('total-withdrawals').textContent = snap.size;
            });

            // Pending Withdrawals (real-time)
            onSnapshot(query(collection(db, "withdrawals"), where("status", "==", "pending")), snap => {
                document.getElementById('pending-withdrawals').textContent = snap.size;
            });

            // Load recent data
            loadRecentWithdrawals();
            loadRecentUsers();
            loadTodayEarnings();
            loadGameStats();
        }

        async function loadRecentWithdrawals() {
            const withdrawalsSnap = await getDocs(query(
                collection(db, "withdrawals"), 
                orderBy("requestedAt", "desc"), 
                limit(5)
            ));
            
            let html = `<table class="w-full">
                <thead><tr><th>User</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>`;
            
            if (withdrawalsSnap.empty) {
                html += `<tr><td colspan="4" class="text-center py-4 text-gray-500">No recent withdrawals</td></tr>`;
            } else {
                withdrawalsSnap.forEach(doc => {
                    const req = doc.data();
                    const date = req.requestedAt?.toDate().toLocaleDateString() || 'N/A';
                    const statusClass = req.status === 'approved' ? 'status-approved' : 
                                      req.status === 'pending' ? 'status-pending' : 'status-rejected';
                    html += `<tr class="border-b">
                        <td class="py-2">${req.userName || 'N/A'}</td>
                        <td class="py-2 font-semibold">${req.amount}</td>
                        <td class="py-2"><span class="status-badge ${statusClass}">${req.status}</span></td>
                        <td class="py-2 text-gray-500 text-sm">${date}</td>
                    </tr>`;
                });
            }
            
            html += `</tbody></table>`;
            document.getElementById('recent-withdrawals').innerHTML = html;
        }

        async function loadRecentUsers() {
            const usersSnap = await getDocs(query(
                collection(db, "users"), 
                orderBy("createdAt", "desc"), 
                limit(5)
            ));
            
            let html = `<table class="w-full">
                <thead><tr><th>Name</th><th>Email</th><th>Balance</th><th>Status</th></tr></thead>
                <tbody>`;
            
            if (usersSnap.empty) {
                html += `<tr><td colspan="4" class="text-center py-4 text-gray-500">No users found</td></tr>`;
            } else {
                usersSnap.forEach(doc => {
                    const user = doc.data();
                    const isBlocked = user.isBlocked || false;
                    const statusClass = isBlocked ? 'status-blocked' : 'status-active';
                    html += `<tr class="border-b">
                        <td class="py-2">${user.name || 'N/A'}</td>
                        <td class="py-2">${user.email || 'N/A'}</td>
                        <td class="py-2 font-semibold">${user.balance || 0}</td>
                        <td class="py-2"><span class="status-badge ${statusClass}">${isBlocked ? 'Blocked' : 'Active'}</span></td>
                    </tr>`;
                });
            }
            
            html += `</tbody></table>`;
            document.getElementById('recent-users').innerHTML = html;
        }

        async function loadTodayEarnings() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const pointHistorySnap = await getDocs(query(
                collection(db, "pointHistory"),
                where("timestamp", ">=", today)
            ));
            
            let totalEarnings = 0;
            pointHistorySnap.forEach(doc => {
                const history = doc.data();
                if (history.points > 0) {
                    totalEarnings += history.points;
                }
            });
            
            document.getElementById('today-earnings').textContent = totalEarnings;
        }

        async function loadGameStats() {
            try {
                // Get game configuration
                const gamesSnap = await getDoc(doc(db, "config", "games"));
                const gamesConfig = gamesSnap.exists() ? gamesSnap.data() : {};
                
                // Get today's game play data
                const today = new Date().toISOString().split('T')[0];
                const todayStats = gamesConfig.dailyStats?.[today] || {};
                
                let html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
                
                // Games list
                const games = [
                    { id: 'colorGame', name: 'Color Game', icon: 'palette', color: 'bg-purple-500' },
                    { id: 'watchVideo', name: 'Watch Video', icon: 'video', color: 'bg-blue-500' },
                    { id: 'ludoDice', name: 'Ludo Dice', icon: 'dice-5', color: 'bg-green-500' }
                ];
                
                games.forEach(game => {
                    const gameData = gamesConfig[game.id] || {};
                    const gameStats = todayStats[game.id] || { playCount: 0, totalPlays: 0, userCount: 0 };
                    const isVisible = gameData.visible !== false;
                    
                    html += `
                        <div class="game-card">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="${game.color} text-white p-2 rounded-lg mr-3">
                                        <i data-lucide="${game.icon}" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold">${game.name}</h3>
                                        <p class="text-sm text-gray-500">${isVisible ? 'Visible' : 'Hidden'}</p>
                                    </div>
                                </div>
                                <span class="status-badge ${isVisible ? 'status-visible' : 'status-hidden'}">
                                    ${isVisible ? 'Visible' : 'Hidden'}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <div>
                                    <p class="text-sm text-gray-500">Today's Plays</p>
                                    <p class="font-bold text-lg">${gameStats.playCount || 0}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Users</p>
                                    <p class="font-bold text-lg">${gameStats.userCount || 0}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Daily Limit</p>
                                    <p class="font-bold text-lg">${gameData.dailyPlayLimit > 0 ? `${gameData.dailyPlayLimit} plays` : 'Unlimited'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Reward</p>
                                    <p class="font-bold text-lg">${gameData.rewardPerPlay || 0} pts</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                document.getElementById('game-stats').innerHTML = html;
                lucide.createIcons();
            } catch (error) {
                console.error("Error loading game stats:", error);
                document.getElementById('game-stats').innerHTML = 
                    `<p class="text-center text-gray-500">Error loading game statistics</p>`;
            }
        }

        // ===== ENHANCED GAME MANAGEMENT (from reference code) =====
        async function loadGames() {
            const page = document.getElementById('games');
            page.innerHTML = `
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Game Management</h1>
                    <div class="flex space-x-2">
                        <button onclick="syncAllGameSettings()" class="btn btn-secondary">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Sync All
                        </button>
                        <button onclick="resetAllGameStats()" class="btn btn-warning">
                            Reset All Game Stats
                        </button>
                    </div>
                </div>
                
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2"></i>
                        <p class="text-blue-700">
                            <strong>Features:</strong> 1) Set Daily Play Limit - Game becomes unplayable after limit reached. 
                            2) Toggle Game Visibility - Hide/show game in mini-app.
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Color Game Card -->
                    <div class="game-card" id="colorGame-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-purple-500 text-white p-3 rounded-lg mr-3">
                                    <i data-lucide="palette" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold">Color Game</h2>
                                    <p class="text-sm text-gray-500">Match colors to earn points</p>
                                </div>
                            </div>
                            <span id="colorGame-status" class="status-badge status-visible">Visible</span>
                        </div>
                        
                        <div class="game-limits-grid">
                            <div class="limit-card">
                                <div class="limit-title">Daily Play Limit</div>
                                <div class="limit-value" id="colorGame-play-limit">20 plays</div>
                            </div>
                            <div class="limit-card">
                                <div class="limit-title">Reward Per Play</div>
                                <div class="limit-value" id="colorGame-reward">10 points</div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold">Today's Plays:</span>
                                <span class="font-bold text-blue-600" id="colorGame-today-plays">0</span>
                            </div>
                        </div>
                        
                        <div class="game-action-buttons">
                            <button onclick="openQuickSettings('colorGame')" class="btn btn-primary flex-1">
                                <i data-lucide="settings" class="w-4 h-4 mr-2"></i> Settings
                            </button>
                        </div>
                    </div>
                    
                    <!-- Watch Video Card -->
                    <div class="game-card" id="watchVideo-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-blue-500 text-white p-3 rounded-lg mr-3">
                                    <i data-lucide="video" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold">Watch Video</h2>
                                    <p class="text-sm text-gray-500">Earn by watching ads</p>
                                </div>
                            </div>
                            <span id="watchVideo-status" class="status-badge status-visible">Visible</span>
                        </div>
                        
                        <div class="game-limits-grid">
                            <div class="limit-card">
                                <div class="limit-title">Daily Play Limit</div>
                                <div class="limit-value" id="watchVideo-play-limit">10 videos</div>
                            </div>
                            <div class="limit-card">
                                <div class="limit-title">Reward Per Video</div>
                                <div class="limit-value" id="watchVideo-reward">15 points</div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold">Today's Plays:</span>
                                <span class="font-bold text-blue-600" id="watchVideo-today-plays">0</span>
                            </div>
                        </div>
                        
                        <div class="game-action-buttons">
                            <button onclick="openQuickSettings('watchVideo')" class="btn btn-primary flex-1">
                                <i data-lucide="settings" class="w-4 h-4 mr-2"></i> Settings
                            </button>
                        </div>
                    </div>
                    
                    <!-- Ludo Dice Card -->
                    <div class="game-card" id="ludoDice-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-green-500 text-white p-3 rounded-lg mr-3">
                                    <i data-lucide="dice-5" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold">Ludo Dice</h2>
                                    <p class="text-sm text-gray-500">Roll dice to win points</p>
                                </div>
                            </div>
                            <span id="ludoDice-status" class="status-badge status-visible">Visible</span>
                        </div>
                        
                        <div class="game-limits-grid">
                            <div class="limit-card">
                                <div class="limit-title">Daily Play Limit</div>
                                <div class="limit-value" id="ludoDice-play-limit">50 plays</div>
                            </div>
                            <div class="limit-card">
                                <div class="limit-title">Reward Per Play</div>
                                <div class="limit-value" id="ludoDice-reward">5 points</div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-semibold">Today's Plays:</span>
                                <span class="font-bold text-blue-600" id="ludoDice-today-plays">0</span>
                            </div>
                        </div>
                        
                        <div class="game-action-buttons">
                            <button onclick="openQuickSettings('ludoDice')" class="btn btn-primary flex-1">
                                <i data-lucide="settings" class="w-4 h-4 mr-2"></i> Settings
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Global Controls -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Global Controls</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold mb-2">Set All Games Limit</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" id="global-play-limit" class="input-field" placeholder="e.g., 20">
                                <button onclick="applyGlobalPlayLimit()" class="btn btn-primary">Apply to All</button>
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2">Set All Games Reward</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" id="global-reward" class="input-field" placeholder="e.g., 10">
                                <button onclick="applyGlobalReward()" class="btn btn-primary">Apply to All</button>
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold mb-2">Visibility Control</label>
                            <div class="flex space-x-2">
                                <button onclick="showAllGames()" class="btn btn-success flex-1">Show All</button>
                                <button onclick="hideAllGames()" class="btn btn-danger flex-1">Hide All</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Game Statistics -->
                <div class="bg-white p-6 rounded-lg shadow mt-6">
                    <h2 class="text-xl font-bold mb-4">Game Statistics</h2>
                    <div id="games-stats-table" class="overflow-x-auto">
                        Loading...
                    </div>
                </div>
            `;
            
            await loadGamesData();
            await loadGamesStats();
        }

        async function loadGamesData() {
            try {
                const gamesSnap = await getDoc(doc(db, "config", "games"));
                const gamesConfig = gamesSnap.exists() ? gamesSnap.data() : {};
                
                // Load game configurations
                const games = [
                    { id: 'colorGame', name: 'Color Game', icon: 'palette', color: 'bg-purple-500' },
                    { id: 'watchVideo', name: 'Watch Video', icon: 'video', color: 'bg-blue-500' },
                    { id: 'ludoDice', name: 'Ludo Dice', icon: 'dice-5', color: 'bg-green-500' }
                ];
                
                // Get today's stats
                const today = new Date().toISOString().split('T')[0];
                const todayStats = gamesConfig.dailyStats?.[today] || {};
                
                games.forEach(game => {
                    const gameConfig = gamesConfig[game.id] || {};
                    const isVisible = gameConfig.visible !== false;
                    const dailyPlayLimit = gameConfig.dailyPlayLimit || 0;
                    const rewardPerPlay = gameConfig.rewardPerPlay || 0;
                    const todayGameStats = todayStats[game.id] || { playCount: 0 };
                    
                    // Update status badge
                    const statusEl = document.getElementById(`${game.id}-status`);
                    if (statusEl) {
                        statusEl.textContent = isVisible ? 'Visible' : 'Hidden';
                        statusEl.className = `status-badge ${isVisible ? 'status-visible' : 'status-hidden'}`;
                    }
                    
                    // Update play limit display
                    const playLimitEl = document.getElementById(`${game.id}-play-limit`);
                    if (playLimitEl) {
                        playLimitEl.textContent = dailyPlayLimit > 0 ? `${dailyPlayLimit} plays` : 'Unlimited';
                    }
                    
                    // Update reward display
                    const rewardEl = document.getElementById(`${game.id}-reward`);
                    if (rewardEl) {
                        rewardEl.textContent = `${rewardPerPlay} points`;
                    }
                    
                    // Update today's plays
                    const todayPlaysEl = document.getElementById(`${game.id}-today-plays`);
                    if (todayPlaysEl) {
                        todayPlaysEl.textContent = todayGameStats.playCount || 0;
                    }
                });
                
                lucide.createIcons();
            } catch (error) {
                console.error("Error loading games data:", error);
                showAlert("❌ Error loading game configurations", true);
            }
        }

        // Quick Game Settings Functions (from reference code)
        function openQuickSettings(gameId) {
            currentEditingGameId = gameId;
            
            const gameNames = {
                'colorGame': 'Color Game',
                'watchVideo': 'Watch Video',
                'ludoDice': 'Ludo Dice'
            };
            
            document.getElementById('quick-game-title').textContent = `${gameNames[gameId]} Settings`;
            
            // Load current settings
            loadGameSettingsForQuickEdit(gameId);
            
            // Show modal
            document.getElementById('quick-game-settings-modal').style.display = 'flex';
        }

        async function loadGameSettingsForQuickEdit(gameId) {
            try {
                const gamesSnap = await getDoc(doc(db, "config", "games"));
                const gamesConfig = gamesSnap.exists() ? gamesSnap.data() : {};
                
                const gameConfig = gamesConfig[gameId] || {};
                
                // Set form values
                document.getElementById('quick-play-limit').value = gameConfig.dailyPlayLimit || 0;
                document.getElementById('quick-game-visible').checked = gameConfig.visible !== false;
                document.getElementById('quick-game-reward').value = gameConfig.rewardPerPlay || 0;
                document.getElementById('quick-game-order').value = gameConfig.order || 1;
                
            } catch (error) {
                console.error("Error loading game settings:", error);
            }
        }

        function closeQuickSettingsModal() {
            document.getElementById('quick-game-settings-modal').style.display = 'none';
            currentEditingGameId = null;
        }

        async function saveQuickGameSettings() {
            if (!currentEditingGameId) return;
            
            const dailyPlayLimit = parseInt(document.getElementById('quick-play-limit').value) || 0;
            const isVisible = document.getElementById('quick-game-visible').checked;
            const rewardPerPlay = parseInt(document.getElementById('quick-game-reward').value) || 0;
            const order = parseInt(document.getElementById('quick-game-order').value) || 1;
            
            if (dailyPlayLimit < 0 || rewardPerPlay < 0) {
                showAlert("❌ Please enter valid positive numbers");
                return;
            }
            
            if (order < 1 || order > 10) {
                showAlert("❌ Order must be between 1 and 10");
                return;
            }
            
            loader.style.display = 'flex';
            try {
                const gameRef = doc(db, "config", "games");
                const gameSnap = await getDoc(gameRef);
                const currentData = gameSnap.exists() ? gameSnap.data() : {};
                
                const updateData = {
                    [currentEditingGameId]: {
                        name: currentEditingGameId === 'colorGame' ? 'Color Game' : 
                              currentEditingGameId === 'watchVideo' ? 'Watch Video' : 'Ludo Dice',
                        dailyPlayLimit: dailyPlayLimit,
                        visible: isVisible,
                        rewardPerPlay: rewardPerPlay,
                        order: order,
                        updatedAt: serverTimestamp()
                    }
                };
                
                await setDoc(gameRef, updateData, { merge: true });
                
                showToast("✅ Game settings saved successfully!");
                closeQuickSettingsModal();
                
                // Refresh displays
                await loadGamesData();
                await loadGamesStats();
                if (document.getElementById('dashboard').classList.contains('active')) {
                    loadGameStats();
                }
                
            } catch (error) {
                console.error("Error saving game settings:", error);
                showAlert("❌ Error saving game settings: " + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        // Global Controls Functions (from reference code)
        async function applyGlobalPlayLimit() {
            const limit = parseInt(document.getElementById('global-play-limit').value) || 0;
            
            if (isNaN(limit) || limit < 0) {
                showAlert("Please enter a valid positive number for the play limit.");
                return;
            }
            
            showConfirm(`Apply daily play limit of ${limit} to ALL games?`, async () => {
                loader.style.display = 'flex';
                try {
                    const gameRef = doc(db, "config", "games");
                    const gameSnap = await getDoc(gameRef);
                    const currentData = gameSnap.exists() ? gameSnap.data() : {};
                    
                    const updateData = { ...currentData };
                    
                    // Update all games
                    ['colorGame', 'watchVideo', 'ludoDice'].forEach(gameId => {
                        if (!updateData[gameId]) updateData[gameId] = {};
                        updateData[gameId].dailyPlayLimit = limit;
                        updateData[gameId].updatedAt = serverTimestamp();
                    });
                    
                    await setDoc(gameRef, updateData);
                    
                    showToast(`✅ Daily play limit of ${limit} applied to all games!`);
                    await loadGamesData();
                    await loadGamesStats();
                    if (document.getElementById('dashboard').classList.contains('active')) {
                        loadGameStats();
                    }
                    
                } catch (error) {
                    console.error("Error applying global limit:", error);
                    showAlert("❌ Error applying global limit", true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        async function applyGlobalReward() {
            const reward = parseInt(document.getElementById('global-reward').value) || 0;
            
            if (isNaN(reward) || reward < 0) {
                showAlert("Please enter a valid positive number for the reward.");
                return;
            }
            
            showConfirm(`Apply reward of ${reward} points to ALL games?`, async () => {
                loader.style.display = 'flex';
                try {
                    const gameRef = doc(db, "config", "games");
                    const gameSnap = await getDoc(gameRef);
                    const currentData = gameSnap.exists() ? gameSnap.data() : {};
                    
                    const updateData = { ...currentData };
                    
                    // Update all games
                    ['colorGame', 'watchVideo', 'ludoDice'].forEach(gameId => {
                        if (!updateData[gameId]) updateData[gameId] = {};
                        updateData[gameId].rewardPerPlay = reward;
                        updateData[gameId].updatedAt = serverTimestamp();
                    });
                    
                    await setDoc(gameRef, updateData);
                    
                    showToast(`✅ Reward of ${reward} points applied to all games!`);
                    await loadGamesData();
                    await loadGamesStats();
                    if (document.getElementById('dashboard').classList.contains('active')) {
                        loadGameStats();
                    }
                    
                } catch (error) {
                    console.error("Error applying global reward:", error);
                    showAlert("❌ Error applying global reward", true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        async function showAllGames() {
            showConfirm("Make ALL games visible in the mini-app?", async () => {
                loader.style.display = 'flex';
                try {
                    const gameRef = doc(db, "config", "games");
                    const gameSnap = await getDoc(gameRef);
                    const currentData = gameSnap.exists() ? gameSnap.data() : {};
                    
                    const updateData = { ...currentData };
                    
                    // Show all games
                    ['colorGame', 'watchVideo', 'ludoDice'].forEach(gameId => {
                        if (!updateData[gameId]) updateData[gameId] = {};
                        updateData[gameId].visible = true;
                        updateData[gameId].updatedAt = serverTimestamp();
                    });
                    
                    await setDoc(gameRef, updateData);
                    
                    showToast("✅ All games are now visible!");
                    await loadGamesData();
                    await loadGamesStats();
                    if (document.getElementById('dashboard').classList.contains('active')) {
                        loadGameStats();
                    }
                    
                } catch (error) {
                    console.error("Error showing all games:", error);
                    showAlert("❌ Error showing all games", true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        async function hideAllGames() {
            showConfirm("Hide ALL games from the mini-app?", async () => {
                loader.style.display = 'flex';
                try {
                    const gameRef = doc(db, "config", "games");
                    const gameSnap = await getDoc(gameRef);
                    const currentData = gameSnap.exists() ? gameSnap.data() : {};
                    
                    const updateData = { ...currentData };
                    
                    // Hide all games
                    ['colorGame', 'watchVideo', 'ludoDice'].forEach(gameId => {
                        if (!updateData[gameId]) updateData[gameId] = {};
                        updateData[gameId].visible = false;
                        updateData[gameId].updatedAt = serverTimestamp();
                    });
                    
                    await setDoc(gameRef, updateData);
                    
                    showToast("✅ All games are now hidden!");
                    await loadGamesData();
                    await loadGamesStats();
                    if (document.getElementById('dashboard').classList.contains('active')) {
                        loadGameStats();
                    }
                    
                } catch (error) {
                    console.error("Error hiding all games:", error);
                    showAlert("❌ Error hiding all games", true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        async function syncAllGameSettings() {
            loader.style.display = 'flex';
            try {
                await loadGamesData();
                await loadGamesStats();
                showToast("✅ All game settings synchronized!");
            } catch (error) {
                console.error("Error syncing game settings:", error);
                showAlert("❌ Error syncing game settings", true);
            } finally {
                loader.style.display = 'none';
            }
        }

        async function resetAllGameStats() {
            showConfirm("Are you sure you want to reset ALL game statistics? This action cannot be undone.", async () => {
                loader.style.display = 'flex';
                try {
                    const gameRef = doc(db, "config", "games");
                    const gameSnap = await getDoc(gameRef);
                    const currentData = gameSnap.exists() ? gameSnap.data() : {};
                    
                    // Keep game configurations but reset stats
                    const updateData = {
                        ...currentData,
                        dailyStats: {},
                        allTimeStats: {},
                        updatedAt: serverTimestamp()
                    };
                    
                    await setDoc(gameRef, updateData);
                    
                    showToast("✅ All game statistics reset successfully!");
                    await loadGamesStats();
                    if (document.getElementById('dashboard').classList.contains('active')) {
                        loadGameStats();
                    }
                } catch (error) {
                    console.error("Error resetting game stats:", error);
                    showAlert("❌ Error resetting game statistics", true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        async function loadGamesStats() {
            try {
                const gamesSnap = await getDoc(doc(db, "config", "games"));
                const gamesConfig = gamesSnap.exists() ? gamesSnap.data() : {};
                
                // Get today's stats
                const today = new Date().toISOString().split('T')[0];
                const todayStats = gamesConfig.dailyStats?.[today] || {};
                
                // Get all time stats
                const allTimeStats = gamesConfig.allTimeStats || {};
                
                let html = `<table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Game</th>
                            <th class="px-6 py-3">Today's Plays</th>
                            <th class="px-6 py-3">Today's Users</th>
                            <th class="px-6 py-3">Today's Points</th>
                            <th class="px-6 py-3">Total Plays</th>
                            <th class="px-6 py-3">Total Users</th>
                            <th class="px-6 py-3">Total Points</th>
                        </tr>
                    </thead>
                    <tbody>`;
                
                const games = [
                    { id: 'colorGame', name: 'Color Game' },
                    { id: 'watchVideo', name: 'Watch Video' },
                    { id: 'ludoDice', name: 'Ludo Dice' }
                ];
                
                let todayTotalPlays = 0;
                let todayTotalUsers = 0;
                let todayTotalPoints = 0;
                let allTimeTotalPlays = 0;
                let allTimeTotalUsers = 0;
                let allTimeTotalPoints = 0;
                
                games.forEach(game => {
                    const todayGameStats = todayStats[game.id] || { playCount: 0, userCount: 0 };
                    const allTimeGameStats = allTimeStats[game.id] || { playCount: 0, userCount: 0 };
                    const gameConfig = gamesConfig[game.id] || {};
                    const rewardPerPlay = gameConfig.rewardPerPlay || 0;
                    
                    const todayPoints = todayGameStats.playCount * rewardPerPlay;
                    const allTimePoints = allTimeGameStats.playCount * rewardPerPlay;
                    
                    todayTotalPlays += todayGameStats.playCount;
                    todayTotalUsers += todayGameStats.userCount;
                    todayTotalPoints += todayPoints;
                    allTimeTotalPlays += allTimeGameStats.playCount;
                    allTimeTotalUsers += allTimeGameStats.userCount;
                    allTimeTotalPoints += allTimePoints;
                    
                    html += `<tr class="bg-white border-b">
                        <td class="px-6 py-4 font-semibold">${game.name}</td>
                        <td class="px-6 py-4">${todayGameStats.playCount}</td>
                        <td class="px-6 py-4">${todayGameStats.userCount}</td>
                        <td class="px-6 py-4">${todayPoints}</td>
                        <td class="px-6 py-4">${allTimeGameStats.playCount}</td>
                        <td class="px-6 py-4">${allTimeGameStats.userCount}</td>
                        <td class="px-6 py-4">${allTimePoints}</td>
                    </tr>`;
                });
                
                // Add totals row
                html += `<tr class="bg-gray-50 font-bold">
                    <td class="px-6 py-4">TOTAL</td>
                    <td class="px-6 py-4">${todayTotalPlays}</td>
                    <td class="px-6 py-4">${todayTotalUsers}</td>
                    <td class="px-6 py-4">${todayTotalPoints}</td>
                    <td class="px-6 py-4">${allTimeTotalPlays}</td>
                    <td class="px-6 py-4">${allTimeTotalUsers}</td>
                    <td class="px-6 py-4">${allTimeTotalPoints}</td>
                </tr>`;
                
                html += `</tbody></table>`;
                document.getElementById('games-stats-table').innerHTML = html;
            } catch (error) {
                console.error("Error loading game stats:", error);
                document.getElementById('games-stats-table').innerHTML = 
                    `<p class="text-center text-gray-500">Error loading game statistics</p>`;
            }
        }

        // Users Functions (keep existing users functions...)
        async function loadUsers() {
            const page = document.getElementById('users');
            page.innerHTML = `
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">User Management</h1>
                    <div class="flex space-x-2">
                        <input type="text" id="user-search" placeholder="Search users..." class="input-field w-64">
                        <button onclick="searchUsers()" class="btn btn-primary">Search</button>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Balance</th>
                                    <th class="px-6 py-3">Country</th>
                                    <th class="px-6 py-3">Daily Ads</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">Loading...</tbody>
                        </table>
                    </div>
                </div>
            `;
            
            await loadUsersData();
            
            // Add search event listener
            document.getElementById('user-search').addEventListener('keyup', (e) => {
                if (e.key === 'Enter') searchUsers();
            });
        }

        async function loadUsersData(searchTerm = '') {
            const configSnap = await getDoc(doc(db, "config", "main"));
            const config = configSnap.exists() ? configSnap.data() : {};
            const dailyAdLimit = config.dailyAdLimit || 100;
            
            const usersSnap = await getDocs(collection(db, "users"));
            let tableHTML = '';
            
            usersSnap.forEach(doc => {
                const user = doc.data();
                const userId = doc.id;
                const isBlocked = user.isBlocked || false;
                const userEmail = user.email || '';
                const userName = user.name || 'N/A';
                
                // Filter by search term
                if (searchTerm && 
                    !userName.toLowerCase().includes(searchTerm.toLowerCase()) && 
                    !userEmail.toLowerCase().includes(searchTerm.toLowerCase())) {
                    return;
                }
                
                const dailyAdsWatched = user.dailyAdCount || 0;
                const country = user.country || 'N/A';
                
                tableHTML += `<tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4">${userName}</td>
                    <td class="px-6 py-4">${userEmail}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold">${user.balance || 0}</span>
                            <button onclick="openEditPointsModal('${userId}', '${userName}', '${userEmail}', ${user.balance || 0})" 
                                    class="btn btn-sm btn-warning">Edit</button>
                            <button onclick="viewUserPointHistory('${userId}', '${userName}')" 
                                    class="view-points-btn btn-sm">View Points</button>
                        </div>
                    </td>
                    <td class="px-6 py-4">${country}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold ${dailyAdsWatched >= dailyAdLimit ? 'text-red-600' : 'text-green-600'}">
                                ${dailyAdsWatched}/${dailyAdLimit}
                            </span>
                            ${dailyAdsWatched >= dailyAdLimit ? 
                                '<span class="text-xs text-red-500">Limit Reached</span>' : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge ${isBlocked ? 'status-blocked' : 'status-active'}">
                            ${isBlocked ? 'Blocked' : 'Active'}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button onclick="toggleUserBlock('${userId}', ${isBlocked})" 
                                    class="btn btn-sm ${isBlocked ? 'btn-success' : 'btn-danger'}">
                                ${isBlocked ? 'Unblock' : 'Block'}
                            </button>
                            <button onclick="resetDailyAds('${userId}')" 
                                    class="btn btn-sm btn-secondary">
                                Reset Ads
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
            
            if (!tableHTML) {
                tableHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">No users found</td></tr>`;
            }
            
            document.getElementById('users-table-body').innerHTML = tableHTML;
        }

        function searchUsers() {
            const searchTerm = document.getElementById('user-search').value;
            loadUsersData(searchTerm);
        }

        // Withdrawals Functions (keep existing withdrawals functions...)
        async function loadWithdrawals() {
            const page = document.getElementById('withdrawals');
            page.innerHTML = `
                <h1 class="text-3xl font-bold mb-6">Withdrawal Requests</h1>
                <div class="mb-4 flex space-x-4">
                    <button onclick="filterWithdrawals('all')" class="btn btn-primary">All</button>
                    <button onclick="filterWithdrawals('pending')" class="btn btn-warning">Pending</button>
                    <button onclick="filterWithdrawals('approved')" class="btn btn-success">Approved</button>
                    <button onclick="filterWithdrawals('rejected')" class="btn btn-danger">Rejected</button>
                </div>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="overflow-x-auto">
                        <div id="withdrawals-table-container">Loading...</div>
                    </div>
                </div>
            `;
            
            await loadWithdrawalsData('all');
        }

        async function loadWithdrawalsData(filter = 'all') {
            let q;
            if (filter === 'all') {
                q = query(collection(db, "withdrawals"), orderBy("requestedAt", "desc"));
            } else {
                q = query(collection(db, "withdrawals"), where("status", "==", filter), orderBy("requestedAt", "desc"));
            }
            
            const withdrawalsSnap = await getDocs(q);
            
            if (withdrawalsSnap.empty) {
                document.getElementById('withdrawals-table-container').innerHTML = 
                    `<p class="text-center p-4 text-gray-500">No withdrawal requests found.</p>`;
                return;
            }

            let tableHTML = `<table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Method</th>
                        <th class="px-6 py-3">Details</th>
                        <th class="px-6 py-3">Country</th>
                        <th class="px-6 py-3">Requested At</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>`;
            
            withdrawalsSnap.forEach(doc => {
                const req = doc.data();
                const requestedDate = req.requestedAt?.toDate().toLocaleString() || 'N/A';
                const statusClass = req.status === 'approved' ? 'status-approved' : 
                                  req.status === 'pending' ? 'status-pending' : 'status-rejected';
                const country = req.country || 'N/A';
                
                tableHTML += `<tr class="bg-white border-b">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <span>${req.userName || 'N/A'}</span>
                            <button onclick="viewUserPointHistory('${req.userId}', '${req.userName}')" 
                                    class="view-points-btn btn-sm">View Points</button>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold">${req.amount}</td>
                    <td class="px-6 py-4">${req.method}</td>
                    <td class="px-6 py-4 truncate max-w-xs" title="${req.paymentDetail}">${req.paymentDetail || 'N/A'}</td>
                    <td class="px-6 py-4">${country}</td>
                    <td class="px-6 py-4 text-gray-500 text-sm">${requestedDate}</td>
                    <td class="px-6 py-4"><span class="status-badge ${statusClass}">${req.status}</span></td>
                    <td class="px-6 py-4">
                        ${req.status === 'pending' ? `
                        <div class="flex space-x-2">
                            <button onclick="processWithdrawal('${doc.id}', 'approved')" class="btn-success text-xs px-2 py-1 rounded">Approve</button>
                            <button onclick="processWithdrawal('${doc.id}', 'rejected')" class="btn-danger text-xs px-2 py-1 rounded">Reject</button>
                        </div>
                        ` : '—'}
                    </td>
                </tr>`;
            });
            
            tableHTML += `</tbody></table>`;
            document.getElementById('withdrawals-table-container').innerHTML = tableHTML;
        }

        function filterWithdrawals(status) {
            loadWithdrawalsData(status);
        }

        // Notifications Functions (keep existing notifications functions...)
        function loadNotifications() {
            const page = document.getElementById('notifications');
            page.innerHTML = `
                <h1 class="text-3xl font-bold mb-6">Send Notification</h1>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="stat-card p-6">
                        <h2 class="text-xl font-bold mb-4">Send to All Users</h2>
                        <div class="mb-4">
                            <label for="notification-title" class="block font-semibold mb-2">Title</label>
                            <input type="text" id="notification-title" class="input-field" placeholder="Notification title">
                        </div>
                        <div class="mb-6">
                            <label for="notification-message" class="block font-semibold mb-2">Message</label>
                            <textarea id="notification-message" rows="4" class="input-field" placeholder="Notification message"></textarea>
                        </div>
                        <button id="send-notification-btn" class="btn btn-primary w-full">Send to All Users</button>
                    </div>
                    
                    <div class="stat-card p-6">
                        <h2 class="text-xl font-bold mb-4">Telegram Bot Configuration</h2>
                        <div class="mb-4">
                            <label for="telegram-bot-token" class="block font-semibold mb-2">Bot Token</label>
                            <input type="text" id="telegram-bot-token" class="input-field" placeholder="Enter bot token">
                        </div>
                        <div class="mb-6">
                            <label for="telegram-chat-id" class="block font-semibold mb-2">Chat ID</label>
                            <input type="text" id="telegram-chat-id" class="input-field" placeholder="Enter chat ID">
                        </div>
                        <button onclick="testTelegramConnection()" class="btn btn-warning w-full mb-2">Test Connection</button>
                        <button onclick="saveTelegramConfig()" class="btn btn-success w-full">Save Configuration</button>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h2 class="text-2xl font-bold mb-4">Notification History</h2>
                    <div id="notification-history" class="bg-white shadow rounded-lg p-4">
                        Loading...
                    </div>
                </div>
            `;
            
            document.getElementById('send-notification-btn').addEventListener('click', sendNotification);
            loadNotificationHistory();
            loadTelegramConfig();
        }

        async function sendNotification() {
            const title = document.getElementById('notification-title').value;
            const message = document.getElementById('notification-message').value;
            
            if (!title || !message) {
                showAlert("Please enter both title and message.");
                return;
            }
            
            loader.style.display = 'flex';
            try {
                // Save to Firebase
                await addDoc(collection(db, "notifications"), {
                    title,
                    message,
                    createdAt: serverTimestamp(),
                    sentToAll: true
                });
                
                // Send to Telegram if configured
                await sendTelegramNotification(title, message);
                
                showToast("✅ Notification sent successfully!");
                document.getElementById('notification-title').value = '';
                document.getElementById('notification-message').value = '';
                loadNotificationHistory();
            } catch (error) {
                console.error("Error sending notification:", error);
                showAlert("❌ Error sending notification: " + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        async function sendTelegramNotification(title, message) {
            const configSnap = await getDoc(doc(db, "config", "telegram"));
            if (!configSnap.exists()) return;
            
            const config = configSnap.data();
            if (!config.botToken || !config.chatId) return;
            
            const telegramMessage = `*${title}*\n\n${message}`;
            
            try {
                const response = await fetch(`https://api.telegram.org/bot${config.botToken}/sendMessage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        chat_id: config.chatId,
                        text: telegramMessage,
                        parse_mode: 'Markdown'
                    })
                });
                
                const result = await response.json();
                if (!result.ok) {
                    console.error("Telegram API error:", result);
                    throw new Error(result.description || 'Telegram API error');
                }
            } catch (error) {
                console.error("Error sending to Telegram:", error);
                throw error;
            }
        }

        async function loadNotificationHistory() {
            const notificationsSnap = await getDocs(query(
                collection(db, "notifications"), 
                orderBy("createdAt", "desc"), 
                limit(10)
            ));
            
            let html = `<table class="w-full">
                <thead><tr><th>Title</th><th>Message</th><th>Sent At</th></tr></thead>
                <tbody>`;
            
            if (notificationsSnap.empty) {
                html += `<tr><td colspan="3" class="text-center py-4 text-gray-500">No notifications sent yet</td></tr>`;
            } else {
                notificationsSnap.forEach(doc => {
                    const notification = doc.data();
                    const sentAt = notification.createdAt?.toDate().toLocaleString() || 'N/A';
                    html += `<tr class="border-b">
                        <td class="py-2 font-semibold">${notification.title}</td>
                        <td class="py-2">${notification.message}</td>
                        <td class="py-2 text-gray-500 text-sm">${sentAt}</td>
                    </tr>`;
                });
            }
            
            html += `</tbody></table>`;
            document.getElementById('notification-history').innerHTML = html;
        }

        async function loadTelegramConfig() {
            const configSnap = await getDoc(doc(db, "config", "telegram"));
            if (configSnap.exists()) {
                const config = configSnap.data();
                document.getElementById('telegram-bot-token').value = config.botToken || '';
                document.getElementById('telegram-chat-id').value = config.chatId || '';
            }
        }

        async function saveTelegramConfig() {
            const botToken = document.getElementById('telegram-bot-token').value;
            const chatId = document.getElementById('telegram-chat-id').value;
            
            if (!botToken || !chatId) {
                showAlert("Please enter both Bot Token and Chat ID.");
                return;
            }
            
            loader.style.display = 'flex';
            try {
                await setDoc(doc(db, "config", "telegram"), {
                    botToken,
                    chatId,
                    updatedAt: serverTimestamp()
                });
                showToast("✅ Telegram configuration saved successfully!");
            } catch (error) {
                console.error("Error saving Telegram config:", error);
                showAlert("❌ Error saving configuration: " + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        async function testTelegramConnection() {
            const botToken = document.getElementById('telegram-bot-token').value;
            const chatId = document.getElementById('telegram-chat-id').value;
            
            if (!botToken || !chatId) {
                showAlert("Please enter both Bot Token and Chat ID to test.");
                return;
            }
            
            loader.style.display = 'flex';
            try {
                // Test bot token
                const botResponse = await fetch(`https://api.telegram.org/bot${botToken}/getMe`);
                const botResult = await botResponse.json();
                
                if (!botResult.ok) {
                    showAlert("❌ Bot connection failed: " + botResult.description, true);
                    return;
                }
                
                // Test sending message
                const messageResponse = await fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        chat_id: chatId,
                        text: "✅ Test message from CashReward Admin Panel",
                        parse_mode: 'Markdown'
                    })
                });
                
                const messageResult = await messageResponse.json();
                if (messageResult.ok) {
                    showAlert("✅ Telegram connection successful! Test message sent.");
                } else {
                    showAlert("❌ Failed to send test message: " + messageResult.description, true);
                }
            } catch (error) {
                showAlert("❌ Error testing connection: " + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        // Settings Functions - UPDATED WITH SPECIAL TASK SYSTEM
        async function loadSettings() {
            const page = document.getElementById('settings');
            page.innerHTML = `
                <h1 class="text-3xl font-bold mb-6">App Settings</h1>
                <div class="space-y-8">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">General & Payment</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="font-semibold">Min Withdrawal (Coins)</label>
                                <input type="number" id="minWithdrawal" class="input-field">
                            </div>
                            <div>
                                <label class="font-semibold">Daily Ad Watch Limit</label>
                                <input type="number" id="dailyAdLimit" class="input-field" value="100">
                                <p class="text-sm text-gray-500 mt-1">Maximum ads a user can watch per day</p>
                            </div>
                            <div>
                                <label class="font-semibold">Ad Watch Reward</label>
                                <input type="number" id="adWatchReward" class="input-field">
                            </div>
                            <div>
                                <label class="font-semibold">Color Game Reward</label>
                                <input type="number" id="colorGameReward" class="input-field">
                            </div>
                            <div>
                                <label class="font-semibold">Ludo Dice Reward</label>
                                <input type="number" id="ludoDiceReward" class="input-field">
                            </div>
                            <div>
                                <label class="font-semibold">Coin Value</label>
                                <div class="flex items-center space-x-2">
                                    <input type="number" id="coinValueCoins" class="input-field" placeholder="Coins">
                                    <span class="text-gray-500">=</span>
                                    <input type="number" id="coinValueInr" class="input-field" placeholder="₹ INR">
                                </div>
                            </div>
                            <div>
                                <label class="font-semibold">Payment Methods (comma separated)</label>
                                <input type="text" id="paymentMethods" class="input-field" placeholder="UPI, Paytm, PhonePe">
                            </div>
                        </div>
                    </div>
                    
                    <!-- SPECIAL TASK SYSTEM -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">Special Task System</h2>
                        
                        <!-- Watch Video Special Task -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-bold text-lg mb-3 text-blue-800">Watch Video Special Task</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="font-semibold">Show after # Videos</label>
                                    <input type="number" id="watchAdSpecialTaskCount" class="input-field" placeholder="e.g., 10">
                                </div>
                                <div>
                                    <label class="font-semibold">Task Reward (Points)</label>
                                    <input type="number" id="watchAdSpecialTaskReward" class="input-field" placeholder="e.g., 100">
                                </div>
                                <div>
                                    <label class="font-semibold">Wait Timer (Seconds)</label>
                                    <input type="number" id="watchAdSpecialTaskTimer" class="input-field" placeholder="e.g., 60">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Color Game Special Task -->
                        <div class="mb-6 p-4 bg-purple-50 rounded-lg">
                            <h3 class="font-bold text-lg mb-3 text-purple-800">Color Game Special Task</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="font-semibold">Show after # Plays</label>
                                    <input type="number" id="colorGameSpecialTaskCount" class="input-field" placeholder="e.g., 20">
                                </div>
                                <div>
                                    <label class="font-semibold">Task Reward (Points)</label>
                                    <input type="number" id="colorGameSpecialTaskReward" class="input-field" placeholder="e.g., 200">
                                </div>
                                <div>
                                    <label class="font-semibold">Wait Timer (Seconds)</label>
                                    <input type="number" id="colorGameSpecialTaskTimer" class="input-field" placeholder="e.g., 45">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ludo Dice Special Task -->
                        <div class="mb-6 p-4 bg-green-50 rounded-lg">
                            <h3 class="font-bold text-lg mb-3 text-green-800">Ludo Dice Special Task</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="font-semibold">Show after # Plays</label>
                                    <input type="number" id="ludoDiceSpecialTaskCount" class="input-field" placeholder="e.g., 30">
                                </div>
                                <div>
                                    <label class="font-semibold">Task Reward (Points)</label>
                                    <input type="number" id="ludoDiceSpecialTaskReward" class="input-field" placeholder="e.g., 150">
                                </div>
                                <div>
                                    <label class="font-semibold">Wait Timer (Seconds)</label>
                                    <input type="number" id="ludoDiceSpecialTaskTimer" class="input-field" placeholder="e.g., 30">
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h4 class="font-bold text-yellow-800 mb-2">How Special Tasks Work:</h4>
                            <ul class="list-disc pl-5 text-sm text-yellow-700 space-y-1">
                                <li>After reaching the specified number of plays, a special task appears</li>
                                <li>User completes the task (watches ad/completes challenge)</li>
                                <li>Must wait the specified timer before claiming reward</li>
                                <li>Earns the special task reward (higher than regular play)</li>
                                <li>Counter resets after completing special task</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">IP Geolocation & Country Restrictions</h2>
                        <div class="mb-4">
                            <label class="font-semibold">IPinfo API Token</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" id="ipinfoApiToken" class="input-field" placeholder="Enter IPinfo API token">
                                <button onclick="testIpinfoApi()" class="btn btn-secondary">Test API</button>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                Leave blank to disable country detection. Get free token from 
                                <a href="https://ipinfo.io/signup" target="_blank" class="text-blue-500 hover:underline">ipinfo.io/signup</a>
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="font-semibold">Allowed Countries (comma separated, empty means all allowed)</label>
                            <input type="text" id="allowedCountries" class="input-field" placeholder="bd, in, pk, us, uk">
                            <p class="text-sm text-gray-500 mt-1">
                                Enter country codes separated by commas. 
                                <span class="font-semibold text-blue-600">If both API token and this field are empty, all countries will be allowed.</span>
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="font-semibold">Blocked Country Message</label>
                            <textarea id="blockedCountryMessage" class="input-field h-24" placeholder="Message shown to users from blocked countries"></textarea>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800 mb-2">How Country Restrictions Work:</h3>
                            <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
                                <li>If API token is empty → Country detection is disabled</li>
                                <li>If allowed countries list is empty → All countries are allowed</li>
                                <li>If both are empty → All countries are allowed (no restrictions)</li>
                                <li>If API token exists and allowed countries list has values → Only listed countries allowed</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Website Visit Management</h2>
                            <button onclick="resetWebsiteVisitHistory()" class="btn btn-danger">Reset All History</button>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Configure websites for "Visit & Earn" section and reset visit history.</p>
                        
                        <div id="websites-to-visit-list" class="space-y-3 mb-4">
                            <!-- Websites will be loaded here -->
                        </div>
                        
                        <button onclick="addNewWebsite()" class="btn btn-primary mb-4">+ Add New Website</button>
                        <button onclick="saveWebsiteConfig()" class="btn btn-success ml-2">Save Websites</button>
                        
                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>Field descriptions:</strong></p>
                            <ul class="list-disc pl-5 mt-2">
                                <li><strong>Name:</strong> Website display name (e.g., Google)</li>
                                <li><strong>Link:</strong> Full URL (e.g., https://www.google.com)</li>
                                <li><strong>Reward:</strong> Points to award (e.g., 10)</li>
                                <li><strong>Timer:</strong> Seconds user must wait on site (e.g., 30)</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">Referral System</h2>
                        <div class="mb-4">
                            <label class="font-semibold">Referral Bonus</label>
                                <input type="number" id="referralBonus" class="input-field">
                        </div>
                        <div class="mb-4">
                            <label class="font-semibold">Referral Rules (one per line)</label>
                            <textarea id="referralRules" class="input-field h-24"></textarea>
                        </div>
                        <div>
                            <label class="font-semibold">Share Text ({CODE} & {APP_LINK})</label>
                            <textarea id="referralShareText" class="input-field h-24"></textarea>
                        </div>
                    </div>
                </div>
                <button onclick="saveAllSettings()" class="btn btn-primary mt-8 px-8 py-3 text-lg">
                    <i data-lucide="save" class="w-5 h-5 mr-2"></i> Save All Settings
                </button>
            `;
            
            // Load existing settings
            await loadSettingsData();
            await loadWebsitesConfig();
        }

        async function loadSettingsData() {
            try {
                const configSnap = await getDoc(doc(db, "config", "main"));
                
                if (!configSnap.exists()) {
                    console.log("No settings found, using defaults");
                    return;
                }
                
                const config = configSnap.data();
                
                // Populate settings fields
                if (config.minWithdrawal !== undefined) {
                    document.getElementById('minWithdrawal').value = config.minWithdrawal;
                }
                
                if (config.dailyAdLimit !== undefined) {
                    document.getElementById('dailyAdLimit').value = config.dailyAdLimit;
                }
                
                if (config.adWatchReward !== undefined) {
                    document.getElementById('adWatchReward').value = config.adWatchReward;
                }
                
                if (config.colorGameReward !== undefined) {
                    document.getElementById('colorGameReward').value = config.colorGameReward;
                }
                
                if (config.ludoDiceReward !== undefined) {
                    document.getElementById('ludoDiceReward').value = config.ludoDiceReward;
                }
                
                if (config.coinValueCoins !== undefined) {
                    document.getElementById('coinValueCoins').value = config.coinValueCoins;
                }
                
                if (config.coinValueInr !== undefined) {
                    document.getElementById('coinValueInr').value = config.coinValueInr;
                }
                
                if (config.paymentMethods && Array.isArray(config.paymentMethods)) {
                    document.getElementById('paymentMethods').value = config.paymentMethods.join(', ');
                }
                
                if (config.referralBonus !== undefined) {
                    document.getElementById('referralBonus').value = config.referralBonus;
                }
                
                if (config.referralRules && Array.isArray(config.referralRules)) {
                    document.getElementById('referralRules').value = config.referralRules.join('\n');
                }
                
                if (config.referralShareText) {
                    document.getElementById('referralShareText').value = config.referralShareText;
                }
                
                // Special Task Settings
                if (config.watchAdSpecialTaskCount !== undefined) {
                    document.getElementById('watchAdSpecialTaskCount').value = config.watchAdSpecialTaskCount;
                }
                
                if (config.watchAdSpecialTaskReward !== undefined) {
                    document.getElementById('watchAdSpecialTaskReward').value = config.watchAdSpecialTaskReward;
                }
                
                if (config.watchAdSpecialTaskTimer !== undefined) {
                    document.getElementById('watchAdSpecialTaskTimer').value = config.watchAdSpecialTaskTimer;
                }
                
                // Color Game Special Task
                if (config.colorGameSpecialTaskCount !== undefined) {
                    document.getElementById('colorGameSpecialTaskCount').value = config.colorGameSpecialTaskCount;
                }
                
                if (config.colorGameSpecialTaskReward !== undefined) {
                    document.getElementById('colorGameSpecialTaskReward').value = config.colorGameSpecialTaskReward;
                }
                
                if (config.colorGameSpecialTaskTimer !== undefined) {
                    document.getElementById('colorGameSpecialTaskTimer').value = config.colorGameSpecialTaskTimer;
                }
                
                // Ludo Dice Special Task
                if (config.ludoDiceSpecialTaskCount !== undefined) {
                    document.getElementById('ludoDiceSpecialTaskCount').value = config.ludoDiceSpecialTaskCount;
                }
                
                if (config.ludoDiceSpecialTaskReward !== undefined) {
                    document.getElementById('ludoDiceSpecialTaskReward').value = config.ludoDiceSpecialTaskReward;
                }
                
                if (config.ludoDiceSpecialTaskTimer !== undefined) {
                    document.getElementById('ludoDiceSpecialTaskTimer').value = config.ludoDiceSpecialTaskTimer;
                }
                
                if (config.ipinfoApiToken) {
                    document.getElementById('ipinfoApiToken').value = config.ipinfoApiToken;
                }
                
                if (config.allowedCountries && Array.isArray(config.allowedCountries)) {
                    document.getElementById('allowedCountries').value = config.allowedCountries.join(', ');
                }
                
                if (config.blockedCountryMessage) {
                    document.getElementById('blockedCountryMessage').value = config.blockedCountryMessage;
                }
                
            } catch (error) {
                console.error("Error loading settings:", error);
                showAlert("❌ Error loading settings", true);
            }
        }

        async function saveAllSettings() {
            loader.style.display = 'flex';
            
            try {
                // Collect all settings from form
                const settings = {
                    minWithdrawal: parseInt(document.getElementById('minWithdrawal').value) || 1000,
                    dailyAdLimit: parseInt(document.getElementById('dailyAdLimit').value) || 100,
                    adWatchReward: parseInt(document.getElementById('adWatchReward').value) || 50,
                    colorGameReward: parseInt(document.getElementById('colorGameReward').value) || 100,
                    ludoDiceReward: parseInt(document.getElementById('ludoDiceReward').value) || 50,
                    coinValueCoins: parseInt(document.getElementById('coinValueCoins').value) || 1000,
                    coinValueInr: parseInt(document.getElementById('coinValueInr').value) || 10,
                    paymentMethods: document.getElementById('paymentMethods').value.split(',').map(s => s.trim()).filter(Boolean),
                    referralBonus: parseInt(document.getElementById('referralBonus').value) || 100,
                    referralRules: document.getElementById('referralRules').value.split('\n').map(s => s.trim()).filter(Boolean),
                    referralShareText: document.getElementById('referralShareText').value.trim() || "Use my code {CODE} to get a bonus. App link: {APP_LINK}",
                    
                    // Special Task Settings
                    watchAdSpecialTaskCount: parseInt(document.getElementById('watchAdSpecialTaskCount').value) || 10,
                    watchAdSpecialTaskReward: parseInt(document.getElementById('watchAdSpecialTaskReward').value) || 100,
                    watchAdSpecialTaskTimer: parseInt(document.getElementById('watchAdSpecialTaskTimer').value) || 60,
                    
                    colorGameSpecialTaskCount: parseInt(document.getElementById('colorGameSpecialTaskCount').value) || 20,
                    colorGameSpecialTaskReward: parseInt(document.getElementById('colorGameSpecialTaskReward').value) || 200,
                    colorGameSpecialTaskTimer: parseInt(document.getElementById('colorGameSpecialTaskTimer').value) || 45,
                    
                    ludoDiceSpecialTaskCount: parseInt(document.getElementById('ludoDiceSpecialTaskCount').value) || 30,
                    ludoDiceSpecialTaskReward: parseInt(document.getElementById('ludoDiceSpecialTaskReward').value) || 150,
                    ludoDiceSpecialTaskTimer: parseInt(document.getElementById('ludoDiceSpecialTaskTimer').value) || 30,
                    
                    ipinfoApiToken: document.getElementById('ipinfoApiToken').value.trim(),
                    allowedCountries: document.getElementById('allowedCountries').value.split(',').map(s => s.trim().toLowerCase()).filter(Boolean),
                    blockedCountryMessage: document.getElementById('blockedCountryMessage').value.trim() || "Sorry, this app is not available in your country.",
                    updatedAt: serverTimestamp()
                };
                
                // Save to Firebase
                await setDoc(doc(db, "config", "main"), settings);
                
                showToast("✅ All settings saved successfully!");
                loadUsers(); // Refresh users to show updated daily ad limit
                
            } catch (error) {
                console.error("Error saving settings:", error);
                showAlert("❌ Error saving settings: " + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        async function loadWebsitesConfig() {
            try {
                const configSnap = await getDoc(doc(db, "config", "main"));
                const config = configSnap.data() || {};
                const websites = config.websitesToVisit || [];
                
                const container = document.getElementById('websites-to-visit-list');
                container.innerHTML = '';
                
                if (websites.length === 0) {
                    // Add default websites
                    const defaultWebsites = [
                        { name: "Google", link: "https://www.google.com", reward: 10, timer: 30 },
                        { name: "YouTube", link: "https://www.youtube.com", reward: 15, timer: 45 },
                        { name: "Facebook", link: "https://www.facebook.com", reward: 20, timer: 60 }
                    ];
                    
                    defaultWebsites.forEach((site, index) => {
                        container.innerHTML += `
                            <div class="website-item">
                                <div class="website-field">
                                    <input type="text" placeholder="Website Name" value="${site.name}" data-field="name">
                                </div>
                                <div class="website-field">
                                    <input type="url" placeholder="https://example.com" value="${site.link}" data-field="link">
                                </div>
                                <div class="website-field">
                                    <input type="number" placeholder="Reward" value="${site.reward}" data-field="reward">
                                </div>
                                <div class="website-field">
                                    <input type="number" placeholder="Timer (sec)" value="${site.timer}" data-field="timer">
                                </div>
                                <button onclick="removeWebsite(this)" class="btn btn-danger">×</button>
                            </div>
                        `;
                    });
                } else {
                    websites.forEach((site, index) => {
                        container.innerHTML += `
                            <div class="website-item">
                                <div class="website-field">
                                    <input type="text" placeholder="Website Name" value="${site.name}" data-field="name">
                                </div>
                                <div class="website-field">
                                    <input type="url" placeholder="https://example.com" value="${site.link}" data-field="link">
                                </div>
                                <div class="website-field">
                                    <input type="number" placeholder="Reward" value="${site.reward}" data-field="reward">
                                </div>
                                <div class="website-field">
                                    <input type="number" placeholder="Timer (sec)" value="${site.timer}" data-field="timer">
                                </div>
                                <button onclick="removeWebsite(this)" class="btn btn-danger">×</button>
                            </div>
                        `;
                    });
                }
            } catch (error) {
                console.error("Error loading website config:", error);
            }
        }

        function addNewWebsite() {
            const container = document.getElementById('websites-to-visit-list');
            container.innerHTML += `
                <div class="website-item">
                    <div class="website-field">
                        <input type="text" placeholder="Website Name" value="" data-field="name">
                    </div>
                    <div class="website-field">
                        <input type="url" placeholder="https://example.com" value="" data-field="link">
                    </div>
                    <div class="website-field">
                        <input type="number" placeholder="Reward" value="10" data-field="reward">
                    </div>
                    <div class="website-field">
                        <input type="number" placeholder="Timer (sec)" value="30" data-field="timer">
                    </div>
                    <button onclick="removeWebsite(this)" class="btn btn-danger">×</button>
                </div>
            `;
        }

        function removeWebsite(button) {
            const item = button.closest('.website-item');
            if (item) {
                item.remove();
            }
        }

        async function saveWebsiteConfig() {
            loader.style.display = 'flex';
            try {
                // Get website configurations from the UI
                const websites = [];
                const websiteItems = document.querySelectorAll('#websites-to-visit-list .website-item');
                
                websiteItems.forEach(item => {
                    const name = item.querySelector('[data-field="name"]').value.trim();
                    const link = item.querySelector('[data-field="link"]').value.trim();
                    const reward = parseInt(item.querySelector('[data-field="reward"]').value) || 10;
                    const timer = parseInt(item.querySelector('[data-field="timer"]').value) || 30;
                    
                    if (name && link) {
                        websites.push({
                            name: name,
                            link: link,
                            reward: reward,
                            timer: timer
                        });
                    }
                });
                
                // Save to Firebase
                const configRef = doc(db, "config", "main");
                await updateDoc(configRef, {
                    websitesToVisit: websites,
                    updatedAt: serverTimestamp()
                });
                
                showToast('✅ Website configurations saved successfully!');
            } catch (error) {
                console.error("Error saving website config:", error);
                showAlert('❌ Error saving website configurations: ' + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        async function resetWebsiteVisitHistory() {
            showConfirm("Are you sure you want to reset website visit history for ALL users? This will allow all users to visit websites again for rewards.", async () => {
                loader.style.display = 'flex';
                try {
                    // Get all users
                    const usersSnap = await getDocs(collection(db, "users"));
                    const batch = writeBatch(db);
                    
                    // Reset visitedWebsitesToday for each user
                    usersSnap.forEach((userDoc) => {
                        const userRef = doc(db, "users", userDoc.id);
                        batch.update(userRef, { 
                            visitedWebsitesToday: [],
                            lastWebsiteVisitDate: '1970-01-01'
                        });
                    });
                    
                    await batch.commit();
                    showToast("✅ Website visit history reset successfully for all users!");
                } catch (error) {
                    console.error("Error resetting website history:", error);
                    showAlert("❌ Error resetting website history: " + error.message, true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        async function testIpinfoApi() {
            const apiToken = document.getElementById('ipinfoApiToken').value.trim();
            
            if (!apiToken) {
                showAlert("Please enter an API token to test.");
                return;
            }
            
            loader.style.display = 'flex';
            try {
                // Test the API with a sample IP
                const response = await fetch(`https://api.ipinfo.io/lite/8.8.8.8?token=${apiToken}`);
                
                if (!response.ok) {
                    throw new Error(`API returned status: ${response.status}`);
                }
                
                const data = await response.text();
                const country = data.trim(); // IPinfo lite API returns just the country code
                
                if (country && country.length === 2) {
                    showAlert(`✅ API Test Successful! Detected country: ${country}`);
                } else {
                    showAlert(`⚠️ API responded but didn't return a valid country code. Response: "${data}"`);
                }
            } catch (error) {
                console.error("IPinfo API test error:", error);
                showAlert(`❌ API Test Failed: ${error.message}. Check your token.`, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        // User Point History Functions
        async function viewUserPointHistory(userId, userName) {
            currentViewingUserId = userId;
            document.getElementById('history-user-name').textContent = userName;
            document.getElementById('history-user-id').textContent = userId;
            
            loader.style.display = 'flex';
            try {
                const historySnap = await getDocs(query(
                    collection(db, "pointHistory"),
                    where("userId", "==", userId),
                    orderBy("timestamp", "desc")
                ));
                
                let totalPoints = 0;
                let html = '';
                
                if (historySnap.empty) {
                    html = `<tr><td colspan="5" class="text-center py-4 text-gray-500">No point history found for this user</td></tr>`;
                } else {
                    historySnap.forEach(doc => {
                        const history = doc.data();
                        const points = history.points || 0;
                        if (points > 0) {
                            totalPoints += points;
                        }
                        
                        const taskLabels = {
                            'ad_watch': 'Ad Watch',
                            'color_game': 'Color Game',
                            'website_visit': 'Website Visit',
                            'referral': 'Referral Bonus',
                            'withdrawal': 'Withdrawal',
                            'admin_adjustment': 'Admin Adjustment',
                            'game_play': 'Game Play',
                            'special_task': 'Special Task'
                        };
                        
                        const timestamp = history.timestamp?.toDate().toLocaleString() || 'N/A';
                        const pointsClass = points > 0 ? 'text-green-600' : 'text-red-600';
                        const country = history.country || 'N/A';
                        
                        html += `<tr class="border-b">
                            <td class="px-4 py-2 text-sm">${timestamp}</td>
                            <td class="px-4 py-2">${taskLabels[history.taskType] || history.taskType}</td>
                            <td class="px-4 py-2 font-semibold ${pointsClass}">${points > 0 ? '+' : ''}${points}</td>
                            <td class="px-4 py-2">${country}</td>
                            <td class="px-4 py-2 text-gray-600">${history.details || ''}</td>
                        </tr>`;
                    });
                }
                
                document.getElementById('total-points-earned').textContent = totalPoints;
                document.getElementById('user-point-history-body').innerHTML = html;
                document.getElementById('user-point-history-modal').style.display = 'flex';
            } catch (error) {
                console.error("Error loading point history:", error);
                showAlert("❌ Error loading point history: " + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        function closePointHistoryModal() {
            document.getElementById('user-point-history-modal').style.display = 'none';
            currentViewingUserId = null;
        }

        // User Management Functions
        async function toggleUserBlock(userId, isBlocked) {
            showConfirm(`Are you sure you want to ${isBlocked ? 'unblock' : 'block'} this user?`, async () => {
                loader.style.display = 'flex';
                try {
                    await updateDoc(doc(db, "users", userId), { isBlocked: !isBlocked });
                    showToast(`✅ User ${isBlocked ? 'unblocked' : 'blocked'} successfully!`);
                    loadUsers();
                } catch (error) {
                    console.error("Error updating user:", error);
                    showAlert("❌ Error updating user status.", true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        async function resetDailyAds(userId) {
            showConfirm("Are you sure you want to reset daily ads for this user?", async () => {
                loader.style.display = 'flex';
                try {
                    await updateDoc(doc(db, "users", userId), { 
                        dailyAdCount: 0,
                        lastAdWatchDate: new Date().toISOString().split('T')[0]
                    });
                    showToast("✅ Daily ads reset successfully!");
                    loadUsers();
                } catch (error) {
                    console.error("Error resetting daily ads:", error);
                    showAlert("❌ Error resetting daily ads.", true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        // Point Editing Functions
        function openEditPointsModal(userId, userName, userEmail, currentBalance) {
            currentEditingUserId = userId;
            document.getElementById('edit-user-name').textContent = userName;
            document.getElementById('edit-user-email').textContent = userEmail;
            document.getElementById('edit-current-balance').textContent = currentBalance;
            document.getElementById('new-balance-input').value = currentBalance;
            document.getElementById('edit-points-modal').style.display = 'flex';
        }

        function closeEditPointsModal() {
            document.getElementById('edit-points-modal').style.display = 'none';
            currentEditingUserId = null;
        }

        async function saveUserPoints() {
            const newBalance = parseInt(document.getElementById('new-balance-input').value);
            
            if (isNaN(newBalance)) {
                showAlert("Please enter a valid number for the balance.");
                return;
            }
            
            loader.style.display = 'flex';
            try {
                // Get current balance and user data
                const userRef = doc(db, "users", currentEditingUserId);
                const userSnap = await getDoc(userRef);
                const userData = userSnap.data();
                const currentBalance = userData.balance || 0;
                
                // Update user balance
                await updateDoc(userRef, { balance: newBalance });
                
                // Add point history record
                await addDoc(collection(db, "pointHistory"), {
                    userId: currentEditingUserId,
                    userName: userData.name || 'Unknown',
                    userEmail: userData.email || '',
                    taskType: 'admin_adjustment',
                    points: newBalance - currentBalance,
                    details: `Admin adjusted balance from ${currentBalance} to ${newBalance}`,
                    timestamp: serverTimestamp(),
                    country: userData.country || 'N/A'
                });
                
                showToast("✅ User points updated successfully!");
                closeEditPointsModal();
                loadUsers();
            } catch (error) {
                console.error("Error updating user points:", error);
                showAlert("❌ Error updating user points: " + error.message, true);
            } finally {
                loader.style.display = 'none';
            }
        }

        // Withdrawal Processing Functions
        async function processWithdrawal(requestId, action) {
            showConfirm(`Are you sure you want to ${action} this withdrawal request?`, async () => {
                loader.style.display = 'flex';
                const batch = writeBatch(db);
                const requestRef = doc(db, "withdrawals", requestId);
                
                try {
                    // Get request data
                    const requestSnap = await getDoc(requestRef);
                    if (!requestSnap.exists()) {
                        throw new Error("Request not found");
                    }
                    
                    const requestData = requestSnap.data();
                    
                    // Update withdrawal status
                    batch.update(requestRef, { 
                        status: action,
                        processedAt: serverTimestamp()
                    });
                    
                    // If rejected, refund coins to user
                    if (action === 'rejected') {
                        const userRef = doc(db, "users", requestData.userId);
                        const userSnap = await getDoc(userRef);
                        
                        if (userSnap.exists()) {
                            const userData = userSnap.data();
                            const currentBalance = userData.balance || 0;
                            batch.update(userRef, { balance: currentBalance + requestData.amount });
                            
                            // Add point history for refund
                            await addDoc(collection(db, "pointHistory"), {
                                userId: requestData.userId,
                                userName: requestData.userName,
                                userEmail: userData.email || '',
                                taskType: 'withdrawal',
                                points: requestData.amount,
                                details: `Refund for rejected withdrawal #${requestId}`,
                                timestamp: serverTimestamp(),
                                country: requestData.country || 'N/A'
                            });
                        }
                    }
                    
                    await batch.commit();
                    showToast(`✅ Withdrawal request ${action} successfully!`);
                    loadWithdrawals();
                    loadDashboardData();
                } catch (error) {
                    console.error("Error processing withdrawal:", error);
                    showAlert("❌ Error processing withdrawal request: " + error.message, true);
                } finally {
                    loader.style.display = 'none';
                }
            });
        }

        // Utility Functions
        function showAlert(message, isError = false) {
            const alertBox = document.getElementById('custom-alert');
            const msgEl = document.getElementById('alert-message');
            if (alertBox && msgEl) {
                msgEl.textContent = message;
                alertBox.classList.remove('hidden');
            }
        }

        function showConfirm(message, onConfirm) {
            document.getElementById('confirm-message').textContent = message;
            const confirmModal = document.getElementById('custom-confirm');
            confirmModal.classList.remove('hidden');
            
            // Create new buttons to avoid duplicate listeners
            const oldOkBtn = document.getElementById('confirm-ok-btn');
            const oldCancelBtn = document.getElementById('confirm-cancel-btn');
            
            const newOkBtn = oldOkBtn.cloneNode(true);
            const newCancelBtn = oldCancelBtn.cloneNode(true);
            
            oldOkBtn.parentNode.replaceChild(newOkBtn, oldOkBtn);
            oldCancelBtn.parentNode.replaceChild(newCancelBtn, oldCancelBtn);
            
            newOkBtn.addEventListener('click', () => {
                confirmModal.classList.add('hidden');
                onConfirm();
            });
            
            newCancelBtn.addEventListener('click', () => {
                confirmModal.classList.add('hidden');
            });
        }

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            
            if (!toast || !toastMessage) return;

            toastMessage.textContent = message;
            toast.className = `fixed top-5 right-5 text-white px-6 py-3 rounded-lg shadow-lg opacity-0 z-50 ${isError ? 'bg-red-600' : 'bg-green-600'}`;
            
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.remove('opacity-0'), 50);

            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 500);
            }, 3000);
        }

        // Make functions available globally
        window.toggleUserBlock = toggleUserBlock;
        window.processWithdrawal = processWithdrawal;
        window.openEditPointsModal = openEditPointsModal;
        window.closeEditPointsModal = closeEditPointsModal;
        window.saveUserPoints = saveUserPoints;
        window.resetDailyAds = resetDailyAds;
        window.searchUsers = searchUsers;
        window.filterWithdrawals = filterWithdrawals;
        window.testTelegramConnection = testTelegramConnection;
        window.saveTelegramConfig = saveTelegramConfig;
        window.viewUserPointHistory = viewUserPointHistory;
        window.closePointHistoryModal = closePointHistoryModal;
        window.testIpinfoApi = testIpinfoApi;
        window.resetWebsiteVisitHistory = resetWebsiteVisitHistory;
        window.addNewWebsite = addNewWebsite;
        window.removeWebsite = removeWebsite;
        window.saveWebsiteConfig = saveWebsiteConfig;
        window.saveAllSettings = saveAllSettings;
        
        // Game Management functions (from reference code)
        window.openQuickSettings = openQuickSettings;
        window.closeQuickSettingsModal = closeQuickSettingsModal;
        window.saveQuickGameSettings = saveQuickGameSettings;
        window.applyGlobalPlayLimit = applyGlobalPlayLimit;
        window.applyGlobalReward = applyGlobalReward;
        window.showAllGames = showAllGames;
        window.hideAllGames = hideAllGames;
        window.syncAllGameSettings = syncAllGameSettings;
        window.resetAllGameStats = resetAllGameStats;
    </script>
</body>
</html>
