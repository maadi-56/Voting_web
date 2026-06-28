<?php 
include_once 'config.php'; 

// Start session if not already running
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}
?>
<?php include_once 'includes/header.php'; ?>

<!-- Styling overrides for the premium dark green hero and corporate aesthetic -->
<style>
    .about-dark-hero {
        background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
        border: 1px solid #065f46;
    }
    .feature-card {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(148, 163, 184, 0.06);
    }
</style>

<main class="max-w-6xl mx-auto p-4 md:p-6 space-y-10 mt-4">
    
    <!-- PREMIUM DARK GREEN HERO SECTION -->
    <section class="about-dark-hero rounded-3xl p-8 md:p-12 text-center relative overflow-hidden shadow-lg">
        <!-- Subtle corporate grid pattern overlay -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <span class="bg-white/10 text-emerald-300 border border-emerald-500/30 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
            Platform Framework Integrity
        </span>
        
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight mt-4 max-w-2xl mx-auto">
            Democratizing Secure Voter Architecture
        </h1>
        
        <p class="text-emerald-100/80 text-sm md:text-base mt-4 max-w-xl mx-auto font-medium leading-relaxed">
            Our platform serves as an optimized, transparent framework engineered to provide direct verification metrics, real-time tracking, and balanced ledger solutions for both regional and provincial assemblies.
        </p>
        
        <div class="mt-8 flex justify-center gap-4">
            <a href="index.php" class="bg-white hover:bg-emerald-50 text-slate-900 text-xs font-bold px-6 py-3 rounded-xl transition shadow-md">
                Live Distribution Dashboard
            </a>
        </div>
    </section>

    <!-- METRICS & CORE VALUES ROW -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Pillar 1: Cryptographic Ledger -->
        <div class="feature-card bg-white border border-slate-200/60 p-6 rounded-2xl shadow-sm space-y-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-lg">
                🛡️
            </div>
            <h3 class="text-sm font-black text-slate-900 tracking-tight">Tamper-Proof Logic</h3>
            <p class="text-slate-500 text-xs leading-relaxed font-medium">
                Every individual ballot processed across active server sessions matches incoming database allocations perfectly, securing system precision trends.
            </p>
        </div>

        <!-- Pillar 2: Live Distribution -->
        <div class="feature-card bg-white border border-slate-200/60 p-6 rounded-2xl shadow-sm space-y-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-lg">
                📊
            </div>
            <h3 class="text-sm font-black text-slate-900 tracking-tight">Real-Time Fluid Tally</h3>
            <p class="text-slate-500 text-xs leading-relaxed font-medium">
                Ditches static visual metrics for dynamic progress bar dividers, splitting data outputs instantly between regional and Punjab-level racing parameters.
            </p>
        </div>

        <!-- Pillar 3: Accessibility Architecture -->
        <div class="feature-card bg-white border border-slate-200/60 p-6 rounded-2xl shadow-sm space-y-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-lg">
                ⚡
            </div>
            <h3 class="text-sm font-black text-slate-900 tracking-tight">Clean Scale Interface</h3>
            <p class="text-slate-500 text-xs leading-relaxed font-medium">
                Engineered with high-end white, soft grey, and slate typography rules to keep interface operations smooth, fast-loading, and completely accessible.
            </p>
        </div>

    </section>

</main>

<?php include_once 'includes/footer.php'; ?>