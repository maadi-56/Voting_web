<?php 
include_once 'config.php'; 

// Start session if not already running
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['voter_id']);

$national_winner = null;
$district_winner = null;
$total_votes = 0;

// Execute queries ONLY if user is authenticated
if ($is_logged_in) {
    // 1. Fetch Punjab Level Leader
    $national_query = "SELECT * FROM candidates WHERE election_level='Punjab Level' ORDER BY votes DESC LIMIT 1";
    $national_result = mysqli_query($conn, $national_query);
    if ($national_result && mysqli_num_rows($national_result) > 0) {
        $national_winner = mysqli_fetch_assoc($national_result);
    }

    // 2. Fetch District Level Leader
    $district_query = "SELECT * FROM candidates WHERE election_level='District Level' ORDER BY votes DESC LIMIT 1";
    $district_result = mysqli_query($conn, $district_query);
    if ($district_result && mysqli_num_rows($district_result) > 0) {
        $district_winner = mysqli_fetch_assoc($district_result);
    }

    // 3. Calculate Cumulative Voter Operations Metrics
    $total_query = "SELECT SUM(votes) as total FROM candidates";
    $total_result = mysqli_query($conn, $total_query);
    if ($total_result) {
        $total_data = mysqli_fetch_assoc($total_result);
        $total_votes = $total_data['total'] ?? 0;
    }
}
?>
<?php include_once 'includes/header.php'; ?>

<!-- High-End Glassmorphism and UI Interaction Layer -->
<style>
    body {
        background-color: #f8fafc;
        background-image: 
            radial-gradient(at 0% 0%, rgba(30, 41, 59, 0.03) 0px, transparent 50%),
            radial-gradient(at 50% 0%, rgba(14, 165, 233, 0.04) 0px, transparent 50%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.03) 0px, transparent 50%);
        min-height: 100vh;
    }
    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(241, 245, 249, 0.8);
    }
    .neon-glow-card-large {
        background: linear-gradient(135deg, #01281f 0%, #031412 50%, #0b1320 100%);
        border: 1px solid rgba(16, 185, 129, 0.25);
        box-shadow: 
            inset 0 1px 2px rgba(255,255,255,0.15), 
            0 30px 60px -20px rgba(2, 36, 28, 0.4);
    }
    .interactive-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .interactive-card:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.08);
        border-color: rgba(14, 165, 233, 0.3);
    }
    .progress-bar-fill {
        background: linear-gradient(90deg, #10b981 0%, #06b6d4 100%);
    }
</style>

<!-- High-Performance Notification Toast Overlays -->
<div id="victory-modal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-xl z-50 flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-sm w-full text-center shadow-2xl border border-slate-100 transform translate-y-4 transition-transform duration-300 relative overflow-hidden" id="modal-container">
        <div class="absolute -right-12 -top-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
        <span class="text-4xl inline-block mb-2 animate-pulse">⚡</span>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Consensus Confirmed</h3>
        <p class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full w-max mx-auto mt-1 tracking-wider uppercase border border-emerald-100">Live Metric Analysis</p>
        
        <div class="my-6 p-4 bg-slate-50 border border-slate-200/60 rounded-2xl">
            <h4 class="text-lg font-black text-slate-800 tracking-tight" id="winner-modal-name">---</h4>
            <p class="text-xs font-bold text-blue-600 tracking-widest uppercase mt-0.5" id="winner-modal-party">---</p>
        </div>
        
        <button onclick="closeModal()" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3.5 rounded-xl shadow-md transition-all active:scale-95">
            Dismiss Broadcast Log
        </button>
    </div>
</div>

<main class="max-w-6xl mx-auto p-4 md:p-6 space-y-10 mt-2">

    <?php if (!$is_logged_in): ?>
        <!-- GUEST GATEWAY MATRIX -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center py-12">
            <div class="lg:col-span-5 space-y-6 text-left">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold tracking-wider text-blue-600 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full uppercase">
                    🔒 Distributed Secure Entry
                </span>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter leading-[1.05]">
                    Real-Time Consensus <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500">Dashboard</span>
                </h1>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    Authenticate to deploy real-time configuration arrays, monitor active cryptographic ledger entries, and access localized dynamic trends securely.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <a href="login.php" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-8 py-3.5 rounded-xl transition shadow-lg shadow-slate-900/10 text-center">
                        Sign In to Dashboard
                    </a>
                    <a href="signup.php" class="w-full sm:w-auto bg-white border border-slate-200 text-slate-700 text-xs font-bold px-8 py-3.5 rounded-xl hover:bg-slate-50 transition text-center shadow-xs">
                        Create Access Token
                    </a>
                </div>
            </div>
            
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="glass-panel p-6 rounded-3xl shadow-xs space-y-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 font-bold">01</div>
                    <h3 class="text-sm font-bold text-slate-900">High-Concurrency Infrastructure</h3>
                    <p class="text-xs text-slate-400 font-medium leading-relaxed">Engineered with optimized background synchronization pipelines to compute real-time structural data feeds smoothly.</p>
                </div>
                <div class="glass-panel p-6 rounded-3xl shadow-xs space-y-3">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 font-bold">02</div>
                    <h3 class="text-sm font-bold text-slate-900">Responsive Visual Layers</h3>
                    <p class="text-xs text-slate-400 font-medium leading-relaxed">Integrated glassmorphic elements built alongside localized Tailwind frameworks to deliver flawless micro-interactions.</p>
                </div>
                <div class="glass-panel p-6 rounded-3xl shadow-xs space-y-3">
                    <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 font-bold">03</div>
                    <h3 class="text-sm font-bold text-slate-900">Distributed Access Control</h3>
                    <p class="text-xs text-slate-400 font-medium leading-relaxed">Session protection systems completely abstract analytical insight records until authentication state processes successfully.</p>
                </div>
                <div class="glass-panel p-6 rounded-3xl shadow-xs space-y-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 font-bold">04</div>
                    <h3 class="text-sm font-bold text-slate-900">Seamless Ledger Pipelines</h3>
                    <p class="text-xs text-slate-400 font-medium leading-relaxed">Database records map intuitively through backend structures, updating parameters efficiently across active nodes.</p>
                </div>
            </div>
        </section>

    <?php else: ?>
        <!-- ADVANCED ENHANCED WIDE & DEEP HERO BANNER -->
        <section class="neon-glow-card-large text-white rounded-[32px] p-8 md:p-12 lg:p-16 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 relative overflow-hidden">
            <!-- Strategic geometric visual patterns -->
            <div class="absolute inset-0 pointer-events-none opacity-[0.03] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:20px_20px]"></div>
            <div class="absolute right-0 top-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute left-1/3 bottom-0 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="space-y-4 z-10 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-[10px] uppercase tracking-widest font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full">
                        Live Ledger Stream Active
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-black tracking-tight text-white leading-none">
                    National Real-Time <br class="hidden md:inline"/>Vote Analytics Platform
                </h1>
                <p class="text-slate-400 text-xs md:text-sm font-medium leading-relaxed max-w-xl">
                    Monitoring authenticated voter transaction workloads and computing real-time proportional distributed leads across provincial and localized district sectors safely.
                </p>
            </div>
            
            <!-- Sleek Command-Center Performance Metrics Block -->
            <div class="flex flex-col sm:flex-row items-stretch gap-4 w-full lg:w-auto z-10 shrink-0 lg:min-w-[360px]">
                <div class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-6 flex-1 shadow-2xl">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Total Units Cast</span>
                    <span class="text-3xl lg:text-4xl font-black text-white mt-1.5 block tracking-tight">
                        <?php echo number_format($total_votes); ?>
                    </span>
                </div>
                <div class="bg-white/[0.03] backdrop-blur-md border border-white/10 rounded-2xl p-6 flex-1 shadow-2xl flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">System Security</span>
                    <div class="mt-4">
                        <span class="text-xs font-black text-emerald-400 flex items-center gap-2 uppercase tracking-wider">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full inline-block shadow-[0_0_8px_#10b981]"></span> 
                            E2E Encrypted
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- HIGH-FIDELITY TRACKING INTERFACE PANEL -->
        <section class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3 pl-1">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 bg-slate-900 rounded-full"></span>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight uppercase">Live Assembly Projections</h2>
                </div>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Sort Matrix: Rank #1</span>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Card 1: Punjab Assembly Trend Leader -->
                <?php if($national_winner && $national_winner['votes'] >= 0): ?>
                    <?php 
                        $national_percentage = $total_votes > 0 ? round(($national_winner['votes'] / $total_votes) * 100, 1) : 0;
                    ?>
                    <div class="interactive-card glass-panel rounded-3xl p-6 flex flex-col justify-between shadow-xs relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/60 uppercase tracking-wide">
                                    🏛️ Punjab Regional Tier
                                </span>
                                <span class="text-[10px] font-black text-slate-400 uppercase bg-slate-100/60 px-2 py-0.5 rounded-md">Live Leader</span>
                            </div>
                            
                            <div class="flex items-center space-x-4 mt-6">
                                <div class="w-14 h-14 rounded-2xl border border-slate-200 shadow-inner bg-slate-100 overflow-hidden shrink-0">
                                    <img src="<?php 
                                        // FIXED: Targeted correct 'image' column identifier for the candidate table
                                        if (!empty($national_winner['image'])) {
                                            echo 'data:image/jpeg;base64,' . base64_encode($national_winner['image']); 
                                        } else {
                                            echo 'uploads/card.webp'; 
                                        }
                                    ?>" 
                                    class="w-full h-full object-cover" 
                                    onerror="this.src='uploads/card.webp'">
                                </div>
                                <div class="space-y-0.5">
                                    <h3 class="text-base font-black text-slate-900 tracking-tight"><?php echo htmlspecialchars($national_winner['name']); ?></h3>
                                    <p class="text-[10px] font-extrabold text-blue-600 tracking-widest uppercase"><?php echo htmlspecialchars($national_winner['party']); ?></p>
                                </div>
                            </div>
                            
                            <!-- Premium Embedded Performance Matrix Visualization -->
                            <div class="mt-6 space-y-1.5">
                                <div class="flex justify-between text-[11px] font-bold text-slate-500">
                                    <span>Relative Volume Density</span>
                                    <span class="text-slate-900"><?php echo $national_percentage; ?>%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden p-[2px] border border-slate-200/40">
                                    <div class="progress-bar-fill h-full rounded-full transition-all duration-500" style="width: <?php echo $national_percentage; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex justify-between items-center">
                            <span class="text-xs text-slate-400 font-medium">Verified Units: <strong class="text-slate-800 font-black"><?php echo number_format($national_winner['votes']); ?></strong></span>
                            <button onclick="triggerWinnerCelebration('<?php echo addslashes($national_winner['name']); ?>', '<?php echo addslashes($national_winner['party']); ?>')" class="text-[11px] font-extrabold bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl transition shadow-xs">
                                View Metrics
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="glass-panel border-dashed rounded-3xl p-6 text-center text-xs text-slate-400 py-12">No profile parameters registered under the Punjab category yet.</div>
                <?php endif; ?>

                <!-- Card 2: District Assembly Trend Leader -->
                <?php if($district_winner && $district_winner['votes'] >= 0): ?>
                    <?php 
                        $district_percentage = $total_votes > 0 ? round(($district_winner['votes'] / $total_votes) * 100, 1) : 0;
                    ?>
                    <div class="interactive-card glass-panel rounded-3xl p-6 flex flex-col justify-between shadow-xs relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-extrabold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200/60 uppercase tracking-wide">
                                    📍 District Sector Tier
                                </span>
                                <span class="text-[10px] font-black text-slate-400 uppercase bg-slate-100/60 px-2 py-0.5 rounded-md">Live Leader</span>
                            </div>
                            
                            <div class="flex items-center space-x-4 mt-6">
                                <div class="w-14 h-14 rounded-2xl border border-slate-200 shadow-inner bg-slate-100 overflow-hidden shrink-0">
                                    <img src="<?php 
                                        // FIXED: Targeted correct 'image' column identifier for the candidate table
                                        if (!empty($district_winner['image'])) {
                                            echo 'data:image/jpeg;base64,' . base64_encode($district_winner['image']); 
                                        } else {
                                            echo 'uploads/card.webp'; 
                                        }
                                    ?>" 
                                    class="w-full h-full object-cover" 
                                    onerror="this.src='uploads/card.webp'">
                                </div>
                                <div class="space-y-0.5">
                                    <h3 class="text-base font-black text-slate-900 tracking-tight"><?php echo htmlspecialchars($district_winner['name']); ?></h3>
                                    <p class="text-[10px] font-extrabold text-blue-600 tracking-widest uppercase"><?php echo htmlspecialchars($district_winner['party']); ?></p>
                                </div>
                            </div>
                            
                            <!-- Premium Embedded Performance Matrix Visualization -->
                            <div class="mt-6 space-y-1.5">
                                <div class="flex justify-between text-[11px] font-bold text-slate-500">
                                    <span>Relative Volume Density</span>
                                    <span class="text-slate-900"><?php echo $district_percentage; ?>%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden p-[2px] border border-slate-200/40">
                                    <div class="progress-bar-fill h-full rounded-full transition-all duration-500" style="width: <?php echo $district_percentage; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex justify-between items-center">
                            <span class="text-xs text-slate-400 font-medium">Verified Units: <strong class="text-slate-800 font-black"><?php echo number_format($district_winner['votes']); ?></strong></span>
                            <button onclick="triggerWinnerCelebration('<?php echo addslashes($district_winner['name']); ?>', '<?php echo addslashes($district_winner['party']); ?>')" class="text-[11px] font-extrabold bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl transition shadow-xs">
                                View Metrics
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="glass-panel border-dashed rounded-3xl p-6 text-center text-xs text-slate-400 py-12">No profile parameters registered under the District category yet.</div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<script>
    function triggerWinnerCelebration(name, party) {
        document.getElementById('winner-modal-name').innerText = name;
        document.getElementById('winner-modal-party').innerText = party;
        
        const modal = document.getElementById('victory-modal');
        const container = document.getElementById('modal-container');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            container.classList.remove('translate-y-4');
            container.classList.add('translate-y-0');
        }, 20);
    }
    
    function closeModal() {
        const modal = document.getElementById('victory-modal');
        const container = document.getElementById('modal-container');
        
        modal.classList.add('opacity-0');
        container.classList.remove('translate-y-0');
        container.classList.add('translate-y-4');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>

<?php include_once 'includes/footer.php'; ?>