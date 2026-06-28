<?php 
if (session_status() == PHP_SESSION_NONE) { session_start(); }
include_once __DIR__ . '/../config.php'; 

// Identify active page to render real-time UI state highlights
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Vote Pakistan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        
        /* High-fidelity custom macro-interactions */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -20px; /* Aligns perfectly with header boundary border */
            left: 0;
            width: 100%;
            height: 2px;
            background: #059669; /* Emerald 600 */
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-link:hover::after, .nav-link.active::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        /* Dropdown transition states */
        .dropdown-menu {
            transform: translateY(10px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col justify-between">
    <header class="w-full bg-white border-b border-slate-200 sticky top-0 z-40 glass-nav shadow-xs">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-6 py-4">
            <!-- Brand Logo Matrix -->
            <a href="index.php" class="flex items-center space-x-2 group">
                <span class="w-3 h-3 bg-emerald-600 rounded-full transition-transform group-hover:scale-125 duration-300"></span>
                <span class="font-bold text-xl tracking-tight text-slate-900">E-Vote<span class="text-emerald-600">Pakistan</span></span>
            </a>
            
            <!-- High-Performance Navigation Menu -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-500">
                <a href="index.php" class="nav-link py-1 <?php echo ($current_page == 'index.php') ? 'text-emerald-600 active' : 'hover:text-slate-900'; ?>">Home</a>
                <a href="analytics.php" class="nav-link py-1 <?php echo ($current_page == 'analytics.php') ? 'text-emerald-600 active' : 'hover:text-slate-900'; ?>">Detailed Analytics</a>
                <?php if(isset($_SESSION["voter_id"])): ?>
                    <a href="ballot.php" class="nav-link py-1 <?php echo ($current_page == 'ballot.php') ? 'text-emerald-600 active' : 'hover:text-slate-900'; ?>">Cast Vote</a>
                <?php endif; ?>
            </nav>

            <!-- Authentication Control Block -->
            <div class="flex items-center space-x-4 text-sm font-medium relative">
                <?php if(isset($_SESSION["voter_id"])): ?>
                    <!-- INTERACTIVE USER DROPDOWN TRIGGER -->
                    <div class="relative">
                        <button id="profileDropdownBtn" class="flex items-center space-x-2.5 bg-slate-50 hover:bg-slate-100 pl-2 pr-3.5 py-1.5 rounded-full border border-slate-200/80 transition active:scale-98 cursor-pointer shadow-2xs">
                            <img src="<?php echo htmlspecialchars($_SESSION['voter_img']); ?>" class="w-7 h-7 rounded-full object-cover border border-slate-300 shadow-inner" onerror="this.src='uploads/card.webp'">
                            <span class="text-xs text-slate-700 font-bold tracking-tight"><?php echo htmlspecialchars($_SESSION["voter_name"]); ?></span>
                            <!-- Modern custom down chevron arrow micro-element -->
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-300" id="dropdownArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- HIGH-FIDELITY GLASSMORPHIC PROFILE PANEL DROPDOWN -->
                        <div id="profileDropdownMenu" class="dropdown-menu absolute right-0 mt-2.5 w-56 bg-white/95 backdrop-blur-xl border border-slate-200/80 rounded-2xl shadow-xl p-2 z-50">
                            <div class="px-3.5 py-3 border-b border-slate-100 mb-1">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Token Access</p>
                                <p class="text-xs font-black text-slate-800 truncate mt-0.5"><?php echo htmlspecialchars($_SESSION["voter_name"]); ?></p>
                                <p class="text-[11px] text-slate-400 font-medium truncate mt-0.5">ID: <?php echo htmlspecialchars($_SESSION["voter_id"]); ?></p>
                            </div>
                            
                            <a href="ballot.php" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition">
                                <span>🗳️</span> <span>Access Live Ballot</span>
                            </a>
                            <a href="analytics.php" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition">
                                <span>📊</span> <span>System Metric Projections</span>
                            </a>
                            
                            <div class="border-t border-slate-100 mt-1.5 pt-1.5">
                                <a href="logout.php" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 transition w-full">
                                    <span>🔒</span> <span>Revoke Access Session</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Dynamic Authentication Actions Layout Tab Matrix -->
                    <div class="flex items-center bg-slate-100/80 border border-slate-200/50 p-1 rounded-xl">
                        <!-- Sign In Button Component -->
                        <a href="login.php" class="px-4 py-2 rounded-lg text-xs font-bold transition duration-200 uppercase tracking-wide <?php echo ($current_page == 'login.php') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900'; ?>">
                            Sign In
                        </a>
                        
                        <!-- Register Button Component -->
                        <a href="signup.php" class="px-4 py-2 rounded-lg text-xs font-bold transition duration-200 uppercase tracking-wide <?php echo ($current_page == 'signup.php' || $current_page == 'index.php') ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:text-slate-900'; ?>">
                            Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- JavaScript Execution Layer handling drop interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownBtn = document.getElementById('profileDropdownBtn');
            const dropdownMenu = document.getElementById('profileDropdownMenu');
            const dropdownArrow = document.getElementById('dropdownArrow');

            if (dropdownBtn && dropdownMenu) {
                // Toggle show matrix execution context
                dropdownBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isExpanded = dropdownMenu.classList.toggle('show');
                    
                    if (isExpanded) {
                        dropdownArrow.style.transform = 'rotate(180deg)';
                    } else {
                        dropdownArrow.style.transform = 'rotate(0deg)';
                    }
                });

                // Dismiss active overlays on viewport target changes
                document.addEventListener('click', function(e) {
                    if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                        dropdownArrow.style.transform = 'rotate(0deg)';
                    }
                });
            }
        });
    </script>