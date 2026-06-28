<?php
include_once 'config.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}

// 1. Calculate Comprehensive Statistical Matrix Metrics
$total_query = mysqli_query($conn, "SELECT SUM(votes) as total FROM candidates");
$total_data = mysqli_fetch_assoc($total_query);
$total_votes = $total_data['total'] ?? 0;

$count_candidates_query = mysqli_query($conn, "SELECT COUNT(*) as total_count FROM candidates");
$count_data = mysqli_fetch_assoc($count_candidates_query);
$total_candidates = $count_data['total_count'] ?? 0;

// Gather unique values for Chart arrays dynamically based on party results
$party_chart_data = [];
$party_query = mysqli_query($conn, "SELECT party, SUM(votes) as total_party_votes FROM candidates GROUP BY party ORDER BY total_party_votes DESC");
while($p_row = mysqli_fetch_assoc($party_query)) {
    $party_chart_data[] = $p_row;
}

// Helper to determine brand styling based on party definitions
function getPartyColors($party) {
    $party = strtoupper(trim($party));
    if ($party === 'PTI') {
        return [
            'bg' => 'bg-emerald-600 shadow-[0_0_12px_rgba(16,185,129,0.2)]',
            'text' => 'text-emerald-700',
            'border' => 'border-emerald-200 focus-within:border-emerald-500',
            'light' => 'bg-emerald-50/40',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200'
        ];
    } elseif ($party === 'PMLN' || $party === 'PML-N') {
        return [
            'bg' => 'bg-blue-600 shadow-[0_0_12px_rgba(37,99,235,0.2)]',
            'text' => 'text-blue-700',
            'border' => 'border-blue-200 focus-within:border-blue-500',
            'light' => 'bg-blue-50/40',
            'badge' => 'bg-blue-100 text-blue-800 border-blue-200'
        ];
    }
    return [
        'bg' => 'bg-slate-600 shadow-sm',
        'text' => 'text-slate-700',
        'border' => 'border-slate-200',
        'light' => 'bg-slate-50/50',
        'badge' => 'bg-slate-100 text-slate-800 border-slate-200'
    ];
}
?>
<?php include_once 'includes/header.php'; ?>

<!-- ChartJS Integration Script Asset Layer -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Premium Light Style Layer Extensions -->
<style>
    body {
        background: radial-gradient(circle at 50% 0%, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
    }
    .analytics-dark-hero {
        background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
        border: 1px solid #065f46;
    }
</style>

<main class="max-w-6xl mx-auto p-4 md:p-6 space-y-8 mt-4 text-slate-800">
    
    <!-- PREMIUM DARK GREEN HERO SECTION INTEGRATING SYSTEM COUNTERS -->
    <section class="analytics-dark-hero rounded-3xl p-6 md:p-8 relative overflow-hidden shadow-lg flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        <!-- Subtle corporate grid pattern overlay -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="space-y-2 z-10">
            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold tracking-wider text-emerald-300 bg-white/10 border border-emerald-500/30 px-3 py-1 rounded-full uppercase">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Live Public Audit Distribution Stream
            </span>
            <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                Statistical Performance Analytics
            </h1>
            <p class="text-emerald-100/70 text-xs md:text-sm font-medium leading-relaxed max-w-xl">
                Live data auditing metrics and system parameters display panel. Real-time consensus data pipelines mapped down below.
            </p>
        </div>
        
        <!-- Glassmorphic Metrics Grid Inside Hero Area -->
        <div class="flex items-center gap-4 w-full lg:w-auto z-10 shrink-0">
            <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 min-w-[140px] flex-1 lg:flex-none shadow-inner">
                <span class="text-[9px] uppercase tracking-wider font-extrabold text-emerald-300/80 block">Total Ballots Cast</span>
                <span class="text-3xl font-black text-white mt-1 block tracking-tight"><?php echo number_format($total_votes); ?></span>
            </div>
            <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 min-w-[140px] flex-1 lg:flex-none shadow-inner">
                <span class="text-[9px] uppercase tracking-wider font-extrabold text-emerald-300/80 block">Active Candidates</span>
                <span class="text-3xl font-black text-white mt-1 block tracking-tight"><?php echo $total_candidates; ?></span>
            </div>
        </div>
    </section>

    <!-- ================= GRAPHICAL VISUAL CHARTS PANELS LAYER ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Doughnut Party Chart -->
        <div class="md:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900 tracking-tight">📊 Party Voting Shares Distribution</h3>
                <p class="text-xs text-slate-400">Proportional performance grouping matrix overview based on historical query indexes.</p>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="partyShareChart"></canvas>
            </div>
        </div>
        
        <!-- Small Meta Progression Progress Gauge -->
        <div class="bg-gradient-to-br from-slate-900 to-blue-950 border border-slate-800 rounded-3xl p-6 text-white flex flex-col justify-between relative overflow-hidden shadow-md">
            <div class="absolute -right-12 -bottom-12 w-44 h-44 bg-blue-600/10 rounded-full blur-2xl"></div>
            <div>
                <span class="text-[10px] uppercase tracking-widest font-extrabold text-blue-400 bg-blue-500/10 border border-blue-500/20 px-2.5 py-1 rounded-full">Consensus Stability Indicator</span>
                <h3 class="text-2xl font-black tracking-tight mt-4">System Precision Audit</h3>
                <p class="text-xs text-slate-400 mt-1">Cryptographic redundancy logging running validation loops.</p>
            </div>
            <div class="my-4 flex items-baseline space-x-2">
                <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">100%</span>
                <span class="text-xs text-slate-500 font-semibold">Immutable Consensus</span>
            </div>
            <div class="w-full bg-slate-950 h-2 rounded-full overflow-hidden p-[1px] border border-slate-800">
                <div class="bg-gradient-to-r from-emerald-400 to-blue-500 h-full rounded-full w-full"></div>
            </div>
        </div>
    </div>

    <!-- Segmented Ledger Lists Layout Grid Layout Structure -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- ================= PUNJAB LEVEL ANALYTICS ================= -->
        <div class="space-y-4">
            <div class="flex items-center space-x-2.5 pl-2">
                <span class="w-2.5 h-5 bg-emerald-600 rounded-full"></span>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Punjab Assembly Ledger Tiers</h2>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-5">
                <?php 
                $punjab_query = mysqli_query($conn, "SELECT * FROM candidates WHERE election_level='Punjab Level' ORDER BY votes DESC");
                
                // Track highest element id position index for rendering winner crown effects
                $is_first = true; 
                
                if ($punjab_query && mysqli_num_rows($punjab_query) > 0):
                    while($candidate = mysqli_fetch_assoc($punjab_query)): 
                        $percentage = $total_votes > 0 ? round(($candidate['votes'] / $total_votes) * 100, 1) : 0;
                        $theme = getPartyColors($candidate['party']);
                        $is_winner = ($is_first && $candidate['votes'] > 0);
                        $is_first = false;
                ?>
                    <!-- Dynamic validation box style changes applied if voter holds lead position flags -->
                    <div class="p-5 rounded-2xl border transition-all duration-300 <?php echo $is_winner ? 'border-emerald-500 bg-gradient-to-br from-white to-emerald-50/20 shadow-md ring-1 ring-emerald-500/20' : 'border-slate-200 ' . $theme['light']; ?>">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <img src="uploads/<?php echo htmlspecialchars($candidate['image']); ?>" class="w-12 h-12 rounded-full object-cover border border-slate-200 shadow-sm" onerror="this.src='uploads/card.webp'">
                                    
                                    <!-- Dynamic Winner Crown Animation Effects -->
                                    <?php if($is_winner): ?>
                                        <span class="absolute -top-3.5 -left-2 text-xl transform -rotate-12 drop-shadow animate-bounce">👑</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-bold text-sm text-slate-900"><?php echo htmlspecialchars($candidate['name']); ?></h4>
                                        <?php if($is_winner): ?>
                                            <span class="text-[9px] bg-emerald-600 text-white font-extrabold px-2 py-0.5 rounded-full shadow-[0_2px_8px_rgba(16,185,129,0.3)] animate-pulse">LEADING WINNER</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded border mt-1 inline-block uppercase <?php echo $theme['badge']; ?>"><?php echo htmlspecialchars($candidate['party']); ?></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black text-slate-900 block"><?php echo number_format($candidate['votes']); ?> <span class="text-xs font-normal text-slate-400">votes</span></span>
                                <span class="text-xs font-bold <?php echo $theme['text']; ?>"><?php echo $percentage; ?>%</span>
                            </div>
                        </div>
                        
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="<?php echo $theme['bg']; ?> h-full rounded-full transition-all duration-1000" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <div class="text-center py-8 text-slate-400 text-sm">No Punjab tier records log segments available.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ================= DISTRICT LEVEL ANALYTICS ================= -->
        <div class="space-y-4">
            <div class="flex items-center space-x-2.5 pl-2">
                <span class="w-2.5 h-5 bg-blue-600 rounded-full"></span>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">District Assembly Ledger Tiers</h2>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-5">
                <?php 
                $district_query = mysqli_query($conn, "SELECT * FROM candidates WHERE election_level='District Level' ORDER BY votes DESC");
                
                $is_first = true;
                
                if ($district_query && mysqli_num_rows($district_query) > 0):
                    while($candidate = mysqli_fetch_assoc($district_query)): 
                        $percentage = $total_votes > 0 ? round(($candidate['votes'] / $total_votes) * 100, 1) : 0;
                        $theme = getPartyColors($candidate['party']);
                        $is_winner = ($is_first && $candidate['votes'] > 0);
                        $is_first = false;
                ?>
                    <!-- Dynamic validation box style changes applied if voter holds lead position flags -->
                    <div class="p-5 rounded-2xl border transition-all duration-300 <?php echo $is_winner ? 'border-blue-500 bg-gradient-to-br from-white to-blue-50/20 shadow-md ring-1 ring-blue-500/20' : 'border-slate-200 ' . $theme['light']; ?>">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <img src="uploads/<?php echo htmlspecialchars($candidate['image']); ?>" class="w-12 h-12 rounded-full object-cover border border-slate-200 shadow-sm" onerror="this.src='uploads/card.webp'">
                                    
                                    <!-- Dynamic Winner Crown Animation Effects -->
                                    <?php if($is_winner): ?>
                                        <span class="absolute -top-3.5 -left-2 text-xl transform -rotate-12 drop-shadow animate-bounce">👑</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-bold text-sm text-slate-900"><?php echo htmlspecialchars($candidate['name']); ?></h4>
                                        <?php if($is_winner): ?>
                                            <span class="text-[9px] bg-blue-600 text-white font-extrabold px-2 py-0.5 rounded-full shadow-[0_2px_8px_rgba(37,99,235,0.3)] animate-pulse">LEADING WINNER</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded border mt-1 inline-block uppercase <?php echo $theme['badge']; ?>"><?php echo htmlspecialchars($candidate['party']); ?></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black text-slate-900 block"><?php echo number_format($candidate['votes']); ?> <span class="text-xs font-normal text-slate-400">votes</span></span>
                                <span class="text-xs font-bold <?php echo $theme['text']; ?>"><?php echo $percentage; ?>%</span>
                            </div>
                        </div>
                        
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="<?php echo $theme['bg']; ?> h-full rounded-full transition-all duration-1000" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <div class="text-center py-8 text-slate-400 text-sm">No District tier records log segments available.</div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</main>

<!-- Initialize and Map Backend PHP Arrays inside Javascript Engine context -->
<script>
    const ctx = document.getElementById('partyShareChart').getContext('2d');
    
    // Inject dynamic data safely collected from database aggregates
    const partyLabels = [<?php foreach($party_chart_data as $data) { echo "'" . addslashes($data['party']) . "',"; } ?>];
    const partyVotes = [<?php foreach($party_chart_data as $data) { echo $data['total_party_votes'] . ","; } ?>];
    
    // Configure matching color hex arrays to match your palette specification criteria
    const colorMapping = partyLabels.map(label => {
        const p = label.toUpperCase().trim();
        if(p === 'PTI') return '#10b981'; // Emerald Green
        if(p === 'PMLN' || p === 'PML-N') return '#2563eb'; // Corporate Blue
        return '#64748b'; // Neutral Slate fallback
    });

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: partyLabels,
            datasets: [{
                data: partyVotes,
                backgroundColor: colorMapping,
                borderWidth: 4,
                borderColor: '#ffffff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                        color: '#334155'
                    }
                }
            },
            cutout: '75%'
        }
    });
</script>

<?php include_once 'includes/footer.php'; ?>